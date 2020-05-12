#!/bin/bash

topics="btalkMainBoards btalkBoardPages btalkMainTopics btalkTopicUrl btalkUserProfiles scrapeResults"

for val in ${topics}; do
    kafka-topics.sh --zookeeper zookeeper:2181 --topic ${val} --describe
done