FROM serversideup/php:8.3-fpm-nginx

USER root

# ── Install Node.js 20 LTS ─────────────────────────────────────────────────────
# The base image has no Node.js. We need it for Vite dev server (HMR).
RUN apt-get update && apt-get install -y \
    gnupg curl ca-certificates && \
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Verify Node.js/npm are installed
RUN node --version && npm --version

# ── Install frontend dependencies ───────────────────────────────────────────────
# node_modules is baked in so native Rollup .node binaries match the container OS.
# Source code is volume-mounted — changes to .blade.php/.js/.css are picked up live.
WORKDIR /var/www/html
COPY package*.json ./
RUN npm install

# ── Storage permissions ───────────────────────────────────────────────────────
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

EXPOSE 8080
