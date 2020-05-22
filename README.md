# Crypto Exchange Rates Tracker

## API DOC
https://app.swaggerhub.com/apis-docs/xcalad01/Bakalarka/0.1

## Running 
> 👉 Note: Docker and Docker-compose need to be installed

* docker-compose up

* docker exec -it bakalarka_workspace_1 bash

* nohup php -S 0.0.0.0:8000 -t public/ > /var/log/bakalarka/server.log &

* service cron start

* chmod -R 777 storage

## Datadog tracing
https://docs.datadoghq.com/tracing/setup/php/

## Init DB:

* php artisan migrate

### Init Data
You can use tmux sessions as adding 8+ years of data is pretty long running process.

#### Init db crypto

* tmux new -s init_db_crypto_task

* php app/Modules/init_db_crypto_task.php

#### Init db fiat

* tmux new -s init_db_fiat_task

* php app/Modules/init_db_fiat_task.php

## Important ENV. Variables
