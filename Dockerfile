FROM php:8.3-fpm

# Set working directory di dalam container
WORKDIR /var/www

# Install dependensi sistem yang dibutuhkan
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

# Clear cache biar containernya ringan
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extension PHP yang wajib buat Laravel & JWT/GraphQL
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Copy Composer dari image resmi Composer ke container kita
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy seluruh source code project ke dalam container
COPY . /var/www

# Atur permission folder storage & cache biar gak error Permission Denied
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Buka port 8000 untuk PHP-FPM
EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]