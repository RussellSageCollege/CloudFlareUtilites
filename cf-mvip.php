#!/usr/bin/env php
<?php
/**
 * Created by PhpStorm.
 * User: melon
 * Date: 9/4/16
 * Time: 4:37 PM
 *
 * Quick and dirty script that finds all DNS records with the specified origin IP address then sets those records to point to the specified destination IP address.
 */
// This function returns a curl object with headers and url
function buildCurl($url, $headers)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    return $ch;
}
// Die if we did not get the right number of args
if (count($argv) < 6) {
    echo 'Usage: cf-mvip "<origin-ip-address>" "<destination-ip-address>" "<target-zone-io>" "<user-email>" "<api-key>"' . "\n";
    die('Usage: cf-mvip "127.0.0.1" "10.0.0.2" "ruyc23ec9zhmexkbvaw9tch7vnbvcmfn" "admin@domian.tld" "prm6rf7ajg459wupzrvj4yrqe2tpvgd8j5pew"' . "\n");
}
/**
 * Initial variables
 */
$originIP = $argv[1]; // The current record ip
$destinationIP = $argv[2]; // The ip that we wan't records to point to
$zoneID = $argv[3]; // You're CF zone
$authEmail = $argv[4]; // CF user email
$apiKey = $argv[5]; // CF api key, keep this safe
$baseURL = 'https://api.cloudflare.com/client/v4/zones/'; // Base api url
$perPage = 100; // Request max results
$page = 1; // Starting page
$dnsRecords = []; // Place to store dns records
$headers = [ 'X-Auth-Email: ' . $authEmail, 'X-Auth-Key: ' . $apiKey, 'Content-Type: ' . 'application/json']; // Build basic headers
/**
 * Start CF Requests
 */
$dnsGetUrl = $baseURL . $zoneID . '/dns_records/?content=' . $originIP . '&page=' . $page . '&per_page=' . $perPage; // Build url to pull initial requests
$curl = buildCurl($dnsGetUrl, $headers); // Build a basic curl object
$result = json_decode(curl_exec($curl)); // Send the request and store the results in a var
$totalPages = $result->result_info->total_pages; // Store the total number of DNS record pages for later
// Loop over each DNS record and store them in an array
foreach ($result->result as $r) {
    $dnsRecords[] = $r; // Store the record in our DNS record array
}
curl_close($curl); // Destroy that curl object, free up memory
// Start a loop that will iterate over DNS record pages, start on the second page
for ($loopPage = 2; $loopPage <= $totalPages; $loopPage++) {
    $dnsGetUrl = $baseURL . $zoneID . '/dns_records/?content=' . $originIP . '&page=' . $loopPage . '&per_page=' . $perPage; // Build url for next page of records
    $curl = buildCurl($dnsGetUrl, $headers); // Build a new curl object
    $result = json_decode(curl_exec($curl)); // Send the request and store the results in a var
    // Loop over each DNS record and store them in an array
    foreach ($result->result as $r) {
        $dnsRecords[] = $r; // Store the record in our DNS record array
    }
    curl_close($curl); // Destroy that curl object, free up memory
}
// Loop over all of the DNS records that we collected and send the record to CF with it's new IP
foreach ($dnsRecords as $record) {
    $dnsUpdateURL = $baseURL . $zoneID . '/dns_records/' . $record->id; // Build url for this record
    $record->content = $destinationIP; // Set the new IP
    $curl = buildCurl($dnsUpdateURL, $headers); // Build a basic curl object
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT'); // Set a custom request method, CF uses PUT to send DNS records
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($record)); // Add the edited record to the curl request as JSON
    $result = json_decode(curl_exec($curl)); // Send the request and store the results in a var
    $status = ($result->success) ? 'success' : 'failure'; // Did everything work out?
    echo $originIP . ' ==> ' . $destinationIP . ' [' . $status . '] (' . $record->name . ")\n"; // log our result
    curl_close($curl); // Destroy that curl object, free up memory
}
