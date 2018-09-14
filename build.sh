#!/bin/bash

cd src

composer self-update
composer update
composer dump-autoload -o

cd ../

docker-compose up --build -d