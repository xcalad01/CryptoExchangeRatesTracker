FROM ubuntu:16.04

LABEL maintainer="Filip Caladi <xcalad01@fit.vutbr.cz>"

ENV LANGUAGE=C.UTF-8
ENV LC_ALL=C.UTF-8
ENV LC_CTYPE=C.UTF-8
ENV LANG=C.UTF-8
ENV TERM xterm

RUN apt-get update
RUN apt-get install -y software-properties-common
RUN add-apt-repository -y ppa:ondrej/php

RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y --allow-downgrades --allow-remove-essential \
        --allow-change-held-packages \
        php7.4-cli \
        php7.4-common \
        php7.4-curl \
        php7.4-intl \
        php7.4-json \
        php7.4-xml \
        php7.4-mbstring \
        php7.4-mysql \
        php7.4-pgsql \
        php7.4-sqlite \
        php7.4-sqlite3 \
        php7.4-zip \
        php7.4-bcmath \
        php7.4-memcached \
        php7.4-gd \
        php7.4-dev \
        pkg-config \
        libcurl4-openssl-dev \
        libedit-dev \
        libssl-dev \
        libxml2-dev \
        xz-utils \
        libsqlite3-dev \
        sqlite3 \
        git \
        curl \
        vim \
        nano \
        postgresql-client \
    && apt-get clean

ARG LARADOCK_PHP_VERSION

# Set Environment Variables
ENV DEBIAN_FRONTEND noninteractive

# Install composer and add its bin to the PATH.
RUN curl -s http://getcomposer.org/installer | php && \
    echo "export PATH=${PATH}:/var/www/vendor/bin" >> ~/.bashrc && \
    mv composer.phar /usr/local/bin/composer

# Source the bash
RUN . ~/.bashrc

###########################################################################
# Set Timezone
###########################################################################

ARG TZ=UTC
ENV TZ ${TZ}

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

###########################################################################
# Composer:
###########################################################################

# Add the composer.json
COPY ./composer.json /root/.composer/composer.json

# Add the auth.json for magento 2 credentials
COPY ./auth.json /root/.composer/auth.json

## Make sure that ~/.composer belongs to laradock
#RUN chown -R laradock:laradock /home/laradock/.composer

# Export composer vendor path
RUN echo "" >> ~/.bashrc && \
    echo 'export PATH="$HOME/.composer/vendor/bin:$PATH"' >> ~/.bashrc


ARG COMPOSER_GLOBAL_INSTALL=false
ENV COMPOSER_GLOBAL_INSTALL ${COMPOSER_GLOBAL_INSTALL}

RUN if [ ${COMPOSER_GLOBAL_INSTALL} = true ]; then \
    composer global install \
;fi

# Export composer vendor path
RUN echo "" >> ~/.bashrc && \
    echo 'export PATH="~/.composer/vendor/bin:$PATH"' >> ~/.bashrc

###########################################################################
# Crontab
###########################################################################

COPY ./crontab /etc/cron.d

RUN chmod -R 644 /etc/cron.d


############################################################################
## Node / NVM:
############################################################################
#
# Check if NVM needs to be installed
ARG NODE_VERSION=node
ENV NODE_VERSION ${NODE_VERSION}
ARG INSTALL_NODE=false
ARG INSTALL_NPM_VUE_CLI=false
ARG NPM_REGISTRY
ENV NPM_REGISTRY ${NPM_REGISTRY}
ENV NVM_DIR /home/bakalarka/.nvm

RUN if [ ${INSTALL_NODE} = true ]; then \
    # Install nvm (A Node Version Manager)
    mkdir -p $NVM_DIR && \
    curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.33.11/install.sh | bash \
        && . $NVM_DIR/nvm.sh \
        && nvm install ${NODE_VERSION} \
        && nvm use ${NODE_VERSION} \
        && nvm alias ${NODE_VERSION} \
        && if [ ${NPM_REGISTRY} ]; then \
        npm config set registry ${NPM_REGISTRY} \
        ;fi \
        && if [ ${INSTALL_NPM_VUE_CLI} = true ]; then \
        npm install -g @vue/cli \
        ;fi \
        && ln -s `npm bin --global` /home/bakalarka/.node-bin \
;fi

############################################################################
## Datadog tracing:
############################################################################
#ARG INSTALL_DATADOG_TRACING=false
#
#RUN if [ ${INSTALL_DATADOG_TRACING} = true ]; then \
#    dpkg -i datadog-php-tracer.deb \
#;fi

# Clean up
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    rm /var/log/lastlog /var/log/faillog

RUN mkdir /var/log/bakalarka

ADD ../entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT /entrypoint.sh

# Set default work directory
WORKDIR /var/www

