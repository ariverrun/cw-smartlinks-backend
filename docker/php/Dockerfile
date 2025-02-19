ARG PHP_VERSION=8.3

FROM php:${PHP_VERSION:-8.3}-fpm

RUN apt-get update \
    && apt-get install -y make libyaml-dev yamllint libpq-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    pdo_pgsql pgsql

RUN pecl install apcu yaml redis && docker-php-ext-enable yaml apcu redis

ENV PHP_VERSION=${PHP_VERSION}

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

CMD ["php", "-S", "0.0.0.0:8000", "public/index.php"]