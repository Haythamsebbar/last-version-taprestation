#!/bin/bash

# Copy Docker environment file
cp /var/www/html/docker/.env.docker /var/www/html/.env

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
while ! mysqladmin ping -h mysql -u laravel_user -plaravel_password --silent; do
    echo "MySQL is not ready yet. Waiting..."
    sleep 2
done

echo "MySQL is ready!"

# Clear Laravel caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Generate application key if not set
php artisan key:generate --force

# Set proper permissions for writable directories only
find /var/www/html/storage -type d -exec chmod 775 {} \;
find /var/www/html/storage -type f -exec chmod 664 {} \;
find /var/www/html/bootstrap/cache -type d -exec chmod 775 {} \;
find /var/www/html/bootstrap/cache -type f -exec chmod 664 {} \;

# Start Apache in the background
echo "Starting Apache..."
apache2-foreground &
APACHE_PID=$!

# Run database migrations and seeders in the background
echo "Starting database setup in background..."
(
    echo "Running database migrations..."
    php artisan migrate --force
    
    echo "Running database seeders..."
    php artisan db:seed --force
    
    # Cache configuration for production
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    echo "Database setup complete!"
) &

# Wait for Apache to finish (keeps container running)
wait $APACHE_PID