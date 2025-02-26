FROM php:8.4.1-fpm-alpine3.21


RUN apk update && apk add \
    libzip-dev \
    libxslt-dev \
    icu-dev \
    oniguruma-dev

RUN docker-php-ext-install \
    pdo pdo_mysql zip xsl intl opcache exif mbstring \
    sysvmsg sysvsem sysvshm


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


WORKDIR /var/www/app