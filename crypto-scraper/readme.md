# Crypto parser

Cryptocurrency address parser made in PHP - Laravel.

Author: Vladislav Bambuch - xbambu03@stud.fit.vutbr.cz

## Run project
Migrate DB tables:
```bash
docker-compose -f common.yml -f migrate.yml up migrate
```
                 
## Install composer dependencies in Docker
Run the container
```bash
docker-compose -f common.yml -f dev.yml up
``` 

Install the dependencies
```bash
docker exec crypto-scraper_laravel_1 composer install
```

## Run consumer 
```bash
docker-compose -f common.yml -f dev.yml run --rm --name consumer_<name> <service> <php command>
```

### Examples
Consumer test
```bash
docker-compose -f common.yml -f dev.yml run test php artisan consumer:test testTopic testGroup 
```

Producer test
```bash
docker-compose -f common.yml -f dev.yml run test php artisan producer:test testTopic 
```

## Dev commands
Stop all test runs
```bash
docker stop $(docker ps | grep test_run | awk '{print $1}')
```