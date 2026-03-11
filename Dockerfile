FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev

RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-install zip \
    && apt-get clean
    
    
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
 && docker-php-ext-install zip
 
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
