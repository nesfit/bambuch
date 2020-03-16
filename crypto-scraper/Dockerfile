#FROM bitnami/laravel
#
#COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
#
#COPY ./ /crypto-scraper
#
#WORKDIR /crypto-scraper
#
#
##RUN ["composer", "install"]
#
#
#CMD [ "php", "artisan", "bct:load_boards" ]

FROM php:7.4.2

RUN apt-get update -y && apt-get install -y openssl zip unzip git libpq-dev librdkafka-dev
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo pdo_pgsql
RUN pecl install rdkafka
RUN docker-php-ext-enable rdkafka
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
#RUN echo "extension=rdkafka.so" >> /usr/local/etc/php/php.ini

WORKDIR /app
COPY . /app
RUN composer install

#CMD php artisan serve --host=0.0.0.0 --port=8181
EXPOSE 8181