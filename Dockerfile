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

# Create storage link (the only safe build-time command)
# NOTE: config:cache, route:cache, view:cache are NOT run here
# They should be run at container startup or via DigitalOcean run command
RUN php artisan storage:link || true

EXPOSE 8080
