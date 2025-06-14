FROM php:8.2-cli

RUN apt-get update \
 && apt-get install -y unzip curl libzip-dev zlib1g-dev libpng-dev libjpeg-dev \
    libfreetype6-dev git mariadb-client libmagickwand-dev openssh-client bash \
    make --no-install-recommends \
 && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql zip exif intl gd \
 && pecl install xdebug \
 && docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer

RUN adduser --uid 1000 --disabled-password --gecos "" --shell /bin/bash appuser

# Install qlty as root
RUN curl https://qlty.sh | bash \
 && cp /root/.qlty/bin/qlty /usr/local/bin/qlty

USER appuser
WORKDIR /var/www/html

RUN git config --global --add safe.directory /var/www/html

CMD ["sleep", "infinity"]
