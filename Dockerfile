FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip git libzip-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*
