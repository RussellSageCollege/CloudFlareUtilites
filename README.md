# CloudFlare Utility Scripts

A set of scripts that help perform tedious tasks on CloudFlare.

## cfmvip

Finds all DNS records with the specified origin IP address then sets those records to point to the specified destination IP address.

```bash
# cfmvip "<origin-ip-address>" "<destination-ip-address>" "<target-zone-io>" "<user-email>" "<api-key>"
cfmvip "127.0.0.1" "10.0.0.2" "ruyc23ec9zhmexkbvaw9tch7vnbvcmfn" "admin@domian.tld" "prm6rf7ajg459wupzrvj4yrqe2tpvgd8j5pew"
```

## cfcp

Finds all DNS records with the specified origin IP address then creates a new record with the destination IP address.

```bash
# cfcp "<origin-ip-address>" "<destination-ip-address>" "<target-zone-io>" "<user-email>" "<api-key>"
cfcp "127.0.0.1" "10.0.0.2" "ruyc23ec9zhmexkbvaw9tch7vnbvcmfn" "admin@domian.tld" "prm6rf7ajg459wupzrvj4yrqe2tpvgd8j5pew"
```
