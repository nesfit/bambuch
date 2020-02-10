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
docker-compose -f common.yml -f dev.yml run --rm test php artisan consumer:test testTopic testGroup 
```

Producer test
```bash
docker-compose -f common.yml -f dev.yml run --rm test php artisan producer:test testTopic 
```

## Dev commands
Stop all test runs
```bash
docker stop $(docker ps | grep test_run | awk '{print $1}')
```

Stop seeding
```bash
docker stop $(docker ps | grep seed_run | awk '{print $1}')
```

Insert some data into DB
```bash
docker-compose -f common.yml -f dev.yml run --rm seed php artisan bitcointalk:load_boards 
docker-compose -f common.yml -f dev.yml run --rm seed php artisan bitcointalk:run_update_boards
docker-compose -f common.yml -f dev.yml run --rm seed php artisan bitcointalk:run_boards 
docker-compose -f common.yml -f dev.yml run --rm seed php artisan bitcointalk:run_main_topics
```

Broken composer autoload
```bash
composer dump-autoload
composer clear-cache
php artisan cache:clear
```