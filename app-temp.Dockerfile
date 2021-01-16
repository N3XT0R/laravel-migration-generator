FROM php:8.0.1-fpm
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
#imagick from source installation
ARG IMAGICK_COMMIT="132a11fd26675db9eb9f0e9a3e2887c161875206"

RUN echo "**** install imagick php extension from source ****" && \
        cd /usr/local/src && \
        git clone https://github.com/Imagick/imagick && \
        cd imagick && \
        git checkout ${IMAGICK_COMMIT} && \
        phpize && \
        ./configure && \
        make && \
        make install && \
        cd .. && \
        rm -rf imagick && \
        docker-php-ext-enable imagick
RUN docker-php-ext-install xdebug
RUN docker-php-ext-enable xdebug
#RUN docker-php-ext-enable imagick
RUN docker-php-ext-configure gd
RUN docker-php-ext-install gd
#Get composer
RUN curl -sS https://getcomposer.org/installer \
                 | php -- --install-dir=/usr/local/bin --filename=composer
