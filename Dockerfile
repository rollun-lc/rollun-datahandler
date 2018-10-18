FROM php:alpine:7.1
FROM composer:latest

WORKDIR /var/www/lib

RUN docker-php-ext-install mysqli && docker-php-ext-install json

COPY composer.json .
COPY composer.lock .

RUN composer install

COPY . .