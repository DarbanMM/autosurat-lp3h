#!/bin/bash
set -e

# Only check database connection if it's local (localhost/postgres)
if [[ "$DB_HOST" == "localhost" || "$DB_HOST" == "postgres" ]]; then
    echo 'Waiting for database connection...'
    until PGPASSWORD=$DB_PASSWORD psql -h "$DB_HOST" -U "$DB_USERNAME" -d "$DB_DATABASE" -c '\q' 2>/dev/null; do
        echo 'Attempting to connect...'
        sleep 2
    done
    echo 'Database is ready!'
else
    echo 'Using external database, skipping connection check...'
fi

# Generate app key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    php artisan key:generate
fi

# Run migrations (skip if database not accessible)
php artisan migrate --force 2>/dev/null || echo 'Migration skipped (database not accessible yet)'

# Clear caches
php artisan config:clear
php artisan cache:clear

echo 'Application is ready!'

exec "$@"
