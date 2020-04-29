# Crypto Corvid

<p align="center">
  <img src="assets/logo.png">
</p>

Crypto Corvid is a platform for collecting and displaying metadata about cryptocurrency addresses. It is written in PHP - Laravel and uses Apache Kafka for communication between particular modules running in Docker containers.

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
docker-compose -f infra.yml up/stop -d
```

Run/stop specific container
```bash
docker-compose -f infra.yml up/stop -d [kafka|graylog|postgres]
```

Serve Laravel app - not using Docker for FE
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## Common modules execution
Run all modules
```bash
docker-compose -f infra.yml -f common.yml up -d
```

Stop all modules
```bash
docker stop $(docker ps | grep common | awk '{print $1}')
```

## Bitcointalk modules execution
Run all modules
```bash
docker-compose -f infra.yml -f bitcointalk-base.yml up -d
```

Run a module  
```bash
docker-compose -f infra.yml -f bitcointalk-base.yml up -d <name> (bct_main_boards_producer)
```

Stop all modules
```bash
docker stop $(docker ps | grep bct | awk '{print $1}')
```

Scaling a module
```bash
docker-compose -f infra.yml -f bitcointalk-base.yml up -d --scale bct_board_pages_producer=5 bct_board_pages_producer
```

## Bitcoinabuse modules execution
Run a modules
```bash
docker-compose -f infra.yml -f bitcoinabuse-base.yml up -d bca_load_csv_data [30d, forever]
```

Stop all modules
```bash
docker stop $(docker ps | grep bca | awk '{print $1}')
```

## Dev commands
Install new dependencies
```bash
docker-compose -f infra.yml -f maintenance.yml run --rm composer require <package>
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

Remove containers
```bash
docker rm $(docker ps -a | grep producer | awk '{print $1}')
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

Generate UML depchart (using https://github.com/mihaeu/dephpend):
```bash
dephpend uml app/Console/ -o out.png --no-classes -d 0 -e '/Models|Psr|Symfony|GuzzleHttp|RdKafka|Illuminate|Tests|Bitcoinabuse|Bitinfocharts|Docker|Constants/' 
```

Generate Docker Compose dep chart (all services has to be in only one yml file):
```bash
docker run --rm -it --name dcv -v $(pwd):/input pmsipilot/docker-compose-viz render -o ./all.png -m image all.yml --no-volumes -f
```