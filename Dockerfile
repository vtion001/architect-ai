FROM php:8.3-fpm

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy code
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Optimize Laravel
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan storage:link

# Permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Use Nginx + PHP-FPM (App Platform provides Nginx proxy)
EXPOSE 8080

CMD ["php-fpm"]