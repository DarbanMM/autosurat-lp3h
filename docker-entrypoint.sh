#!/bin/bash
set -e

# Wait for database to be ready
until PGPASSWORD=$DB_PASSWORD psql -h "$DB_HOST" -U "$DB_USERNAME" -d "$DB_DATABASE" -c '\q' 2>/dev/null; do
    echo 'Waiting for database...'
    sleep 1
done

echo 'Database is ready!'

# Generate app key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    php artisan key:generate
fi

# Run migrations
php artisan migrate --force

# Clear caches
php artisan config:clear
php artisan cache:clear

echo 'Application is ready!'

exec "$@"
