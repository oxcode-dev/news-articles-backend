# Use the official PHP image with the desired version (PHP 8.1 or 8.2)
FROM php:8.1-fpm

# Set working directory inside container
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    git \
    unzip \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql

# Install Composer (dependency manager)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the Laravel project into the container
COPY . /var/www

# Set proper file permissions
RUN chown -R www-data:www-data /var/www

# Expose the default PHP-FPM port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
