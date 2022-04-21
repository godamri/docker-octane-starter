#!/bin/bash

git clone https://github.com/godamri/docker-octane-starter.git ./
rm ./get.sh
rm -rf ./.git
cd ./appsrc && cp .env.example .env && composer install