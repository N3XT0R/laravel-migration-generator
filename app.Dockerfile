FROM php:8.2-cli

RUN apt-get update \
 && apt-get install -y unzip curl libzip-dev zlib1g-dev libpng-dev libjpeg-dev \
    libfreetype6-dev git mariadb-client libmagickwand-dev openssh-client bash \
    make unixodbc-dev libpq-dev gnupg2 --no-install-recommends \
 && rm -rf /var/lib/apt/lists/*

# Microsoft ODBC Driver for SQL Server (Linux)
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
 && curl https://packages.microsoft.com/config/ubuntu/20.04/prod.list | tee /etc/apt/sources.list.d/mssql-release.list \
 && apt-get update \
 && ACCEPT_EULA=Y apt-get install -y msodbcsql17


# PHP Extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install zip exif intl gd pdo_mysql pdo_pgsql \
 && pecl install sqlsrv pdo_sqlsrv xdebug \
 && docker-php-ext-enable sqlsrv pdo_sqlsrv xdebug

# Composer
RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer

# User setup
RUN adduser --uid 1000 --disabled-password --gecos "" --shell /bin/bash appuser

# Install qlty as root
RUN curl https://qlty.sh | bash \
 && cp /root/.qlty/bin/qlty /usr/local/bin/qlty

USER appuser
WORKDIR /var/www/html

RUN git config --global --add safe.directory /var/www/html

CMD ["sleep", "infinity"]
