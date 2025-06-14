FROM php:8.2-cli
RUN apt-get update \
    && apt-get install -y unzip curl libzip-dev zlib1g-dev libpng-dev libjpeg-dev \
    libfreetype6-dev git mariadb-client libmagickwand-dev openssh-client \
    make --no-install-recommends
#ext-installs without dependencies
RUN docker-php-ext-install pdo_mysql zip \
    && docker-php-ext-install exif \
    && docker-php-ext-install intl
#PECL installs
RUN pecl install xdebug
#Enabling exts/installing exts with configurations
RUN docker-php-ext-enable xdebug
RUN docker-php-ext-install gd
#Get composer
RUN curl -sS https://getcomposer.org/installer \
                 | php -- --install-dir=/usr/local/bin --filename=composer
# add user
RUN adduser --disabled-password --gecos "" appuser
RUN chown -R appuser:appuser /var/www/html

# change to secured user
USER appuser