FROM php:7.2-fpm
RUN apt-get update \
    && apt-get install -y unzip curl libzip-dev zlib1g-dev libpng-dev libjpeg-dev libfreetype6-dev git mariadb-client libmagickwand-dev openssh-client --no-install-recommends
RUN docker-php-ext-install pdo_mysql zip \
    && pecl install imagick \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install exif \
    && docker-php-ext-install intl \
    && docker-php-ext-enable imagick \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd \
    && curl -sS https://getcomposer.org/installer \
                 | php -- --install-dir=/usr/local/bin --filename=composer
