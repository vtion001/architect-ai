FROM serversideup/php:8.3-fpm-nginx

USER root

# ── Install Node.js 20 LTS (base image has none) ────────────────────────────
RUN apt-get update && apt-get install -y gnupg curl ca-certificates && \
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# ── PHP dependencies (layer-cached before source copy) ───────────────────────
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# ── Frontend dependencies + production build (layer-cached) ─────────────────
COPY package.json package-lock.json* ./
RUN npm ci && npm run build

# ── Application source ───────────────────────────────────────────────────────
COPY . .

# Run post-install scripts now that full source is present
RUN composer run-script post-autoload-dump 2>/dev/null || true

# ── Storage permissions ───────────────────────────────────────────────────────
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8080

# Healthcheck — used by Render and container orchestrators
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
  CMD curl -f http://localhost/healthcheck || exit 1

ENTRYPOINT ["./docker/entrypoint.sh"]
# No CMD — entrypoint.sh defaults to supervisord (nginx + php-fpm).
# Render overrides via startCommand; queue service overrides via docker-compose `command:`.
