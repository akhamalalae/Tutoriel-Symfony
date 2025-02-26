FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo pdo_mysql

COPY docker/php/php.ini /usr/local/etc/php/

# Ajout de l'installation de Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer

# Installer Node.js et npm
RUN apt-get update && apt-get install -y \
    nodejs \
    npm

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

RUN apt-get update && apt-get install -y librabbitmq-dev libssh-dev \
    && docker-php-ext-install opcache bcmath sockets \
    && pecl install amqp \
    && docker-php-ext-enable amqp
    
# Nettoyage pour réduire la taille de l'image
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

