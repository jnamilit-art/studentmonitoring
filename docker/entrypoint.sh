#!/usr/bin/env bash
set -e

# Substitute dynamic PORT for Render
PORT="${PORT:-80}"
echo "Configuring Nginx on port: $PORT"
sed -i "s/PORT_PLACEHOLDER/$PORT/g" /etc/nginx/http.d/default.conf

# Ensure storage directories exist and have proper permissions
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create storage symlink
php artisan storage:link --force || true

# Optimize cache in production if APP_KEY is provided
if [ -n "$APP_KEY" ]; then
    echo "Caching Laravel configuration and routes..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Run database migrations if enabled
if [ "${AUTO_MIGRATE:-false}" = "true" ] || [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || true
fi

echo "Starting Supervisor (Nginx + PHP-FPM)..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
