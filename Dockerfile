FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    unzip \
    zip \
    curl \
    git \
    cron \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl pdo pdo_pgsql zip \
    && apt-get clean \
    && ln -s /usr/local/bin/php /usr/bin/php

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install

RUN a2enmod rewrite

COPY ./apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
