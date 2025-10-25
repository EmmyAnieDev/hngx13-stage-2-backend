#!/bin/bash

echo "🚀 Starting Laravel container..."

# Just wait a bit for MySQL to be ready (healthcheck handles this)
sleep 10

# Generate app key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:placeholder" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

echo "Running migrations..."
php artisan migrate --force || echo "⚠️ Migrations failed, continuing..."

echo "Caching configuration..."
php artisan config:cache
php artisan route:cache

echo "✅ Laravel setup complete!"
echo "Starting PHP-FPM..."
php-fpm
