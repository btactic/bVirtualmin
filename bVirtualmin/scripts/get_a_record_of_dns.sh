#!/bin/bash

DNS_SERVER=8.8.8.8
DOMAIN=$1

dig @${DNS_SERVER} $DOMAIN A +short | grep -E "^([0-9]+\.){3}[0-9]+$"
