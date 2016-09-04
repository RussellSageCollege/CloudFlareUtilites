# CloudFlare Utility Scripts

A set of scripts that help perform tedious tasks on CloudFlare.

## Move DNS Record IP: cf-mvip

Finds all DNS records with the specified origin ip address then sets those records to point to the specified destination ip address.

```bash
# cf-mvip "<origin-ip-address>" "<destination-ip-address>" "<target-zone-io>" "<user-email>" "<api-key>"
cf-mvip "127.0.0.1" "10.0.0.2" "ruyc23ec9zhmexkbvaw9tch7vnbvcmfn" "admin@domian.tld" "prm6rf7ajg459wupzrvj4yrqe2tpvgd8j5pew"
```
