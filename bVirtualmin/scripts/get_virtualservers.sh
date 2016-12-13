#!/bin/bash

sudo virtualmin list-domains --multiline | awk '

    /^([a-z]+.?)+$/ {
        printf "{\"virtualserver\":\"" $1 "\","
    }

    /^[ \t\n]*Description:/ {
        printf "\"description\":\""
        for(i=2;i<NF;i++) {
            printf "%s ", $i
        };
        printf "%s", $i "\","
    }

    /^[ \t\n]*Created on:/ {
        printf "\"created_on\":\"" $3 " " $4 "\","
    }

    /^[ \t\n]*Disabled:/ {
        printf "\"disabled\":true,"
        printf "\"disabled_description\":\""
        for(i=2;i<NF;i++) {
            printf "%s ", $i
        };
        printf "%s", $i "\","
    }

    /^[ \t\n]*IP address:/ {
        printf "\"ip\":\"" $3 "\","
    }

    /^[ \t\n]*Server quota:/ {
        printf "\"quota\":\"" $3 $4 "\","
    }

    /^[ \t\n]*Server quota used:/ {
        printf "\"used_quota\":\"" $4 $5 "\","
    }

    /^[ \t\n]*Databases size:/ {
        printf "\"databases_size\":\"" $3 $4 "\"}\n"
    }

'
