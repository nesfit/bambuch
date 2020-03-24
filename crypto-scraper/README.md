# Crypto parser

Cryptocurrency address parser made in PHP - Laravel.

Author: Vladislav Bambuch - xbambu03@stud.fit.vutbr.cz

## Build the project
```bash
docker build . -t crypto_scraper_laravel:latest
```

## Run the project
Migrate DB tables:
```bash
docker-compose -f common.yml -f migrate.yml up migrate
```

Seed DB tables:
```bash
docker-compose -f common.yml -f migrate.yml up seed
```

Run/stop common containers (Kafka, Zookeeper, Graylog, Postgres)
```bash
php artisan [start|stop]
```

Run/stop specific container
```bash
php artisan [kafka|graylog|postgres]:[start|stop]
```

Serve Laravel app
```bash
docker-compose -f common.yml -f dev.yml up -d serve
```

## Bitcointalk commands
Run/stop modules
```bash
php artisan bct:start/stop
```
             
## Composer in Docker
Install the dependencies
```bash
docker-compose -f common.yml -f dev.yml run --rm composer
```

Install new dependencies
```bash
docker-compose -f common.yml -f dev.yml run --rm composer require <package>
```

## Run a consumer 
Common example
```bash
docker-compose -f common.yml -f dev.yml run --rm --name consumer_<name> <service> <artisan command>
```

Consumer test
```bash
docker-compose -f common.yml -f dev.yml run --rm test consumer:test 
```

Producer test
```bash
docker-compose -f common.yml -f dev.yml run --rm test producer:test 
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

Stop everything
```bash
docker stop $(docker ps | grep crypto | awk '{print $1}')
```
```bash
docker-compose -f common.yml -f dev.yml stop
```

Insert some data into DB
```bash
docker-compose -f common.yml -f dev.yml run --rm seed bct:initialize_boards
docker-compose -f common.yml -f dev.yml run --rm seed bct:run_boards 
docker-compose -f common.yml -f dev.yml run --rm seed bct:run_main_topics
```

Broken composer autoload
```bash
composer dump-autoload
composer clear-cache
php artisan cache:clear
```

## Additional notes
OSX docker daemon.json location: `~/.docker/daemon.json`