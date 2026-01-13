FROM serversideup/php:8.3-fpm-nginx

# Install additional system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory (serversideup images use /var/www/html by default)
WORKDIR /var/www/html

# Copy code
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Fix permissions
RUN chown -R webuser:webuser /var/www/html/storage /var/www/html/bootstrap/cache

# Optimize Laravel (DO NOT cache config - it should read from env vars at runtime)
RUN php artisan route:cache \
    && php artisan view:cache \
    && php artisan storage:link

EXPOSE 8080
