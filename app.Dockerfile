FROM php:8.1-fpm
RUN apt-get update \
    && apt-get install -y unzip curl libzip-dev zlib1g-dev libpng-dev libjpeg-dev \
    libfreetype6-dev git mariadb-client libmagickwand-dev openssh-client \
    make --no-install-recommends
#ext-installs without dependencies
RUN docker-php-ext-install pdo_mysql zip \
    && docker-php-ext-install exif \
    && docker-php-ext-install intl
#PECL installs
#RUN pecl install imagick \
#    && pecl install xdebug
#Enabling exts/installing exts with configurations
#RUN docker-php-ext-enable xdebug
#RUN docker-php-ext-enable imagick
#RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/
RUN docker-php-ext-install gd
#Get composer
RUN curl -sS https://getcomposer.org/installer \
                 | php -- --install-dir=/usr/local/bin --filename=composer
