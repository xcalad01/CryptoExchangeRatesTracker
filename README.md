# Crypto Exchange Rates Tracker

## API DOC
https://app.swaggerhub.com/apis-docs/xcalad01/Bakalarka/0.1

## Running 
> 👉 Note: Docker and Docker-compose need to be installed

* docker-compose up

* docker exec -it workspace

* nohup php -S 0.0.0.0:8000 -t public/ > /var/log/bakalarka/server.log &

* php artisan migrate

* ./after_fresh_migration.sh

* service cron start

* chmod -R 777 storage

* npm run dev

After that, the system starts to collect the data in regular intervals

## Datadog tracing
https://docs.datadoghq.com/tracing/setup/php/

## Init DB:

* php artisan migrate

### Init Data
If no database snapshot is available, you can use tmux sessions as adding 8+ years of data is pretty long running process.

#### Init db crypto

* tmux new -s init_db_crypto_task

* export INIT_CRYPTO_START={start_timestamp}

* export INIT_CRYPTO_END={end_timestamp}

* php app/Modules/init_db_crypto_task.php

#### Init db fiat
If you did not do it on startup.

* tmux new -s init_db_fiat_task

* export FIAT_START={start_timestamp}

* export FIAT_END={end_timestamp}

* php artisan fiat:init_db

## Test API
> 👉 Note: phpunit is required to be installed
* from root folder -> ./vendor/bin/phpunit

## Important ENV Variables

* MIX_API_URL => api url
* MIX_API_PORT => api port

* DB_HOST
* DB_DATABASE => name of the database
* DB_USERNAME
* DB_PASSWORD


