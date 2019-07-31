# Bitinfocharts parser

Cryptocurrency address parser made in PHP - Laravel.

Author: Vladislav Bambuch - xbambu03@stud.fit.vutbr.cz

## Run project
Prepare DB tables:
```bash
  php artisan migrate --force 
```
Insert categories into the DB:
```bash
  php artisan db:seed --force
```
Run the project script:
```bash
  php artisan parse:infocharts [1|2|3] #optional verbose argument (default=1)
```

## Features
* Support multiple cryptocurrencies.
* Crawling through the web and finding useful wallets.
* Inserting only new addresses.
* Inserting new identities for existing addresses with different identities.
* Category recognition according to www.walletexplorer.com.
* Immediate data insert after finding.
* Detailed command line outputs about parsing and inserting.

## Algorithm overview
1. Load pages from a config according to a cryptocurrency.
1. Iterate over all pages from the config.
1. Get cryptoaddresses from a page according to the cryptocurrency.
1. Get links to known wallets from the page.
1. Get cryptoaddresses from a wallet page.
1. Create new owner or get existing.
1. Get category based on owner name.
1. Create new identity.
1. Insert new addresses into a database.

### Possible parsing cases 
* A wallet has some addresses on its page.
* A wallet has no addresses on its page. => Only an address from original page will be added.
* A wallet page couldn't be retrieved due to network timeout. => Only the original address will be added.

## TODO
* Retry failed requests.
* Calculate `maxPage` automatically from DOM difference.
* Add more cryptocurrencies from the web.
                 