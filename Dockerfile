FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        curl \
        git \
        unzip \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        libcurl4-openssl-dev \
        default-mysql-client \
    && docker-php-ext-install pdo_mysql mysqli mbstring curl gd zip \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . /var/www/html
COPY docker/apache/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/99-godashop.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-interaction --prefer-dist --no-progress --no-scripts --no-plugins \
    && chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
