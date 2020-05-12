#!/bin/bash

rep_fact=1
parts=10

topics="btalkMainBoards btalkBoardPages btalkMainTopics btalkTopicUrl btalkUserProfiles scrapeResults"

for val in ${topics}; do
    kafka-topics.sh --zookeeper zookeeper:2181 --topic ${val}  --create --partitions ${parts} --replication-factor ${rep_fact}; 
    echo ""
done