FROM serversideup/php:8.3-fpm-nginx

USER root

# Fix storage permissions at build time
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

EXPOSE 8080
