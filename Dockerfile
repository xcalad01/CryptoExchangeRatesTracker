FROM php:latest

RUN apt-get update

RUN apt-get install -y libpq-dev cron

COPY cron_base /etc/cron.d/cron_jobs

# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/cron_jobs

# Apply cron job
RUN crontab /etc/cron.d/cron_jobs

# Create the log file to be able to run tail
RUN touch /var/log/cron.log


RUN docker-php-ext-install pdo pdo_pgsql
