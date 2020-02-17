# Crypto parser

Cryptocurrency address parser made in PHP - Laravel.

Author: Vladislav Bambuch - xbambu03@stud.fit.vutbr.cz

## Run project
Migrate DB tables:
```bash
docker-compose -f common.yml -f migrate.yml up migrate
```

Run the containers
```bash
docker-compose -f common.yml -f dev.yml up
``` 

Serve Laravel app
```bash
docker-compose -f common.yml -f dev.yml up -d serve
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

## Run consumer 
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

Insert some data into DB
```bash
docker-compose -f common.yml -f dev.yml run --rm seed bitcointalk:initialize_boards
docker-compose -f common.yml -f dev.yml run --rm seed bitcointalk:run_boards 
docker-compose -f common.yml -f dev.yml run --rm seed bitcointalk:run_main_topics
```

Broken composer autoload
```bash
composer dump-autoload
composer clear-cache
php artisan cache:clear
```

## Additional notes
OSX docker daemon.json location: `~/.docker/daemon.json`