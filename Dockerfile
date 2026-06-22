FROM php:8.3-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libzip-dev \
    libxml2-dev

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ✅ Copy composer files DULU (supaya layer cache efisien)
COPY composer.json composer.lock /var/www/

# ✅ Install dependencies SEBELUM copy semua file
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Baru copy semua file project
COPY . /var/www

# ✅ Generate autoload setelah semua file ada
RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]