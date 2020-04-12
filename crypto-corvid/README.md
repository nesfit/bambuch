# Crypto Corvid

![logo](assets/logo.png)

Cryptocurrency address parser made in PHP - Laravel.

Author: Vladislav Bambuch - xbambu03@stud.fit.vutbr.cz


## Build the project
```bash
docker build . -t crypto_scraper_laravel:latest
```

## Run the project
Install the dependencies
```bash
docker-compose -f infra.yml -f maintenance.yml run --rm composer
```

Migrate DB tables
```bash
docker-compose -f infra.yml -f maintenance.yml up migrate
```

Seed DB tables
```bash
docker-compose -f infra.yml -f maintenance.yml up seed
```

Run/stop infra containers (Kafka, Zookeeper, Graylog, Postgres...)
```bash
php artisan [start|stop]
 OR
docker-compose -f infra.yml up/stop -d
```

Run/stop specific container
```bash
php artisan [kafka|graylog|postgres]:[start|stop]
```

Serve Laravel app - not using Docker for FE
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## Bitcointalk commands
Run/stop modules
```bash
php artisan bct:start/stop
```
Run a module  
```bash
docker-compose -f infra.yml -f backend.yml run --rm scraper bct:<name>
```
             
## Run a consumer 
Common example
```bash
docker-compose -f infra.yml -f backend.yml run --rm --name consumer_<name> <service> <artisan command>
```

Consumer test
```bash
docker-compose -f infra.yml -f backend.yml run --rm scraper consumer:test 
```

Producer test
```bash
docker-compose -f infra.yml -f backend.yml run --rm scraper producer:test 
```

## Dev commands
Install new dependencies
```bash
docker-compose -f infra.yml -f maintenance.yml run --rm composer require <package>
```

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
docker-compose -f infra.yml -f backend.yml stop
```

Broken composer autoload
```bash
composer dump-autoload
composer clear-cache
php artisan cache:clear
```

## Additional notes
OSX docker daemon.json location: `~/.docker/daemon.json`

CRONTAB entry:
```bash
PATH=/usr/local/bin
* * * * * cd ~/<proj_path>/crypto-corvid/src && php artisan schedule:run >/tmp/cron.stdout.log 2>/tmp/cron.stderr.log
```