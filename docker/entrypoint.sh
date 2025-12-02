#!/bin/sh

set -e

echo "Fixing permissions..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/database
chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/database

# Clear any root-owned log files
rm -f /var/www/storage/logs/*.log

echo "Waiting for database to be ready..."
until nc -z postgres 5432; do
  echo "Postgres not ready, waiting..."
  sleep 1
done


echo "Running database migrations..."
php artisan migrate --force --no-interaction

echo "Generating application key if needed..."
php artisan key:generate --force --no-interaction

echo "Optimizing application (skipping route cache due to duplicate routes)..."
php artisan config:cache
php artisan view:cache

# Only seed on first run (if no users exist)
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();")
if [ "$USER_COUNT" -eq "0" ]; then
    echo "Creating admin user (first run)..."
    php artisan db:seed --force --class=DatabaseSeeder --no-interaction || true
else
    echo "Skipping seeding - users already exist"
fi

echo "Final permission fix..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

echo "Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
