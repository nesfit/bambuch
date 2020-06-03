# Crypto Corvid

<p align="center">
  <img src="assets/logo.png">
</p>

Crypto Corvid is a platform for collecting and displaying metadata about cryptocurrency addresses. It is written in PHP - Laravel and uses Apache Kafka for communication between particular modules running in Docker containers.

Author: Vladislav Bambuch - xbambu03@stud.fit.vutbr.cz


## Build the project
Build frontend image
```bash
DOCKER_BUILDKIT=1 docker build . -t crypto_corvid_frontend -f Dockerfile-fe
```
Build backend image
```bash
DOCKER_BUILDKIT=1 docker build . -t crypto_corvid_backend -f Dockerfile-be
```

## Required files
 - `.env` in the `src/` folder
 - `license.json` in the `docker/[prod, dev]` folders (https://lenses.io/lenses-download)
 - `proxies.json` in the `proxy/` folder with the following structure:
 ```json
[
  {
    "IPAddress": "<proxy IP address>",
    "Port": "<proxy port>"
  }
]
```

## Run the project
### Production
Run the web app
```bash
docker-compose -f infra.yml -f frontend.yml up serve
```

### Development
Run the web app
```bash
php artisan serve --host=0.0.0.0 --port=8000
```
Install the dependencies
```bash
docker-compose -f infra.yml -f maintenance.yml up composer
```

### Common for both envs
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
docker-compose -f infra.yml up/stop
```
Run/stop specific container
```bash
docker-compose -f infra.yml up/stop -d --no-deps [kafka|graylog|postgres]
```


## Kafka management
### Production
Create topics in running Kafka
```bash
docker exec -it -w /scripts prod_kafka_1 bash create-topics.sh 
```

### Development
Create a topic
```bash
kafka-topics.sh --zookeeper zookeeper:2181 --topic testTopic1 --create --partitions 10 --replication-factor 1
```
Delete a topic
```bash
kafka-topics.sh --zookeeper zookeeper:2181 --topic testTopic1 --delete
```
Describe a topic
```bash
kafka-topics.sh --describe --zookeeper zookeeper:2181 --topic btalkMainTopics
```
Alter a topic
```bash
kafka-topics.sh --zookeeper zookeeper:2181 --topic btalkMainTopics --alter --partitions 6
```
List topics
```bash
kafka-topics.sh --zookeeper zookeeper:2181 --list
```
Run host shell script in Kafka
```bash
docker exec -it -w /scripts dev_kafka_1 bash alter-topics.sh 
```
Change group offset
```bash
kafka-consumer-groups.sh --bootstrap-server kafka:9092 --group btalkBoardPagesGroupLoad --reset-offsets --to-earliest --all-topics --execute
```


## Common modules execution
Run all modules - commons modules HAS to be run before any source-specific modules 
```bash
docker-compose -f infra.yml -f common.yml up -d --no-deps
```
Stop all modules
```bash
docker stop $(docker ps | grep common | awk '{print $1}')
```


## Bitcointalk modules execution
Run all modules
```bash
docker-compose -f infra.yml -f bitcointalk-base.yml up -d --no-deps
```
Run a module  
```bash
docker-compose -f infra.yml -f bitcointalk-base.yml up -d --no-deps <name> (bct_main_boards_producer)
```
Stop all modules
```bash
docker stop $(docker ps | grep bct | awk '{print $1}')
```
Scaling a module (when scaling up the SCRAPER_TIMEOUT has to be increased)
```bash
docker-compose -f infra.yml -f bitcointalk-base.yml up -d --no-deps --scale bct_board_pages_producer=5 bct_board_pages_producer
```
Unparsed data into Kafka
```bash
docker-compose -f infra.yml -f bitcointalk-reproducers.yml up -d --no-deps bct_un_board_pages_producer
```

## Bitcoinabuse modules execution
Run a modules
```bash
docker-compose -f infra.yml -f bitcoinabuse-base.yml up -d --no-deps bca_load_csv_data [_30d, _forever]
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
* * * * * cd ~/<proj_path>/crypto-corvid/src && APP_ENV=production php artisan schedule:run >/tmp/cron.stdout.log 2>/tmp/cron.stderr.log
```

Generate UML depchart (using https://github.com/mihaeu/dephpend):
```bash
dephpend uml app/Console/ -o out.png --no-classes -d 0 -e '/Models|Psr|Symfony|GuzzleHttp|RdKafka|Illuminate|Tests|Bitcoinabuse|Bitinfocharts|Docker|Constants/' 
```

Generate Docker Compose dep chart (all services has to be in only one yml file):
```bash
docker run --rm -it --name dcv -v $(pwd):/input pmsipilot/docker-compose-viz render -o ./all.png -m image all.yml --no-volumes -f
```

## License
This project is licensed under the terms of the MIT license.