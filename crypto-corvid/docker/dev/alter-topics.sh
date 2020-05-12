#!/bin/bash

parts=6

topics="btalkMainBoards btalkBoardPages btalkMainTopics btalkTopicUrl btalkUserProfiles scrapeResults"

for val in ${topics}; do
    kafka-topics.sh --zookeeper zookeeper:2181 --topic ${val} --alter --partitions ${parts}; 
    echo ""
done