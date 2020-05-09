#!/bin/bash

echo "starting API"
nohup php -S 0.0.0.0:8000 -t public/ > /var/log/bakalarka/server.log &
echo "API started"

echo "starting cron service"
service cron start
echo "cron service started"
