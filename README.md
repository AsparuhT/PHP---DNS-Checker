# PHP---DNS-Checker

## Overview

DNS Checker is a lightweight PHP application designed to provide real-time DNS lookup and validation. It allows users to query DNS records for any domain, helping in diagnosing DNS-related issues and ensuring domain names are correctly resolving.

## Features

**DNS Record Lookup**: Supports multiple types of DNS records including A, AAAA, MX, TXT, and more.

**User-friendly** Interface: Simple and intuitive web interface for easy operation.

**Real-time Results**: Fetches DNS records directly from the server to ensure the most current information.

**DNSSEC record check**: Performs and provides the results for the existance of a DNSSEC recocord, which is useful when troublelshooting issuies with SSL cerfiticate instalation. For best result the check is performed trought an API call to - https://dns.google/

**Quick Links to Whois and MX toolbox**: The aplication provides direct links to tools that allows for quick and easy check of the domain and its email related DNS records (SPF, DMARC) 
