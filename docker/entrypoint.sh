#!/bin/bash
set -e

# Fix storage permissions (named volume may have different ownership from build)
chown -R 1000:1000 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# If custom command is passed (e.g. "php artisan queue:work"), run it directly
if [ $# -gt 0 ]; then
    exec "$@"
else
    # Start supervisord (nginx + php-fpm in one container)
    exec /usr/bin/supervisord -c /etc/supervisord.conf
fi
