# Crypto Exchange Rates Tracker

## API DOC
https://app.swaggerhub.com/apis-docs/xcalad01/Bakalarka/0.1

## How to run
cd ~/bakalarka

docker-compose up -d nginx

docker exec -it bakalarka_workspace_1 bash

nohup php -S 0.0.0.0:8000 -t public/ > /var/log/bakalarka/server.log &

service cron start

chmod -R 777 storage

### Datadog tracing
https://docs.datadoghq.com/tracing/setup/php/


