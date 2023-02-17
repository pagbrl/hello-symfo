FROM php:8.2-fpm-alpine as builder

RUN apk update \
  && apk add git

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY ./ /hello/
WORKDIR /hello

RUN cp /hello/.env.dist /hello/.env
RUN composer install

FROM php:8.2-apache as production
LABEL service=hello-symfo

RUN apt-get update \
  && apt-get install -y libpq-dev \
  && docker-php-ext-install pdo pdo_pgsql calendar bcmath

RUN a2enmod rewrite
COPY config/apache.conf /etc/apache2/sites-enabled/000-default.conf

COPY --from=builder /hello /hello
WORKDIR /hello/
