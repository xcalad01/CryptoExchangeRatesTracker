#!/bin/bash
echo "run compose install"
composer install
echo "composer innstall finished"

echo "starting API"
nohup php -S 0.0.0.0:8000 -t public/ > /var/log/bakalarka/server.log &
echo "API started"

echo "starting migrations"
php artisan migrate
echo "migration finished"

echo "initializing db"
php artisan crypto:add
php artisan fiat:init_db
php artisan exchange:add
echo "db initialized"

echo "starting cron service"
service cron start
echo "cron service started"

echo "starting stroage permisions"
chmod -R 777 storage
echo "storage permissions finished"

echo "started npm"
npm install && npm run dev
echo "npm finished"
