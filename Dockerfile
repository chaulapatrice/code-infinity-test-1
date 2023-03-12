FROM composer:latest
RUN docker-php-source extract
RUN apk add build-base autoconf
RUN apk add curl-dev openssl-dev
RUN pecl install mongodb 
RUN docker-php-ext-enable mongodb 
RUN apk del build-base
WORKDIR /code
COPY ./src /code