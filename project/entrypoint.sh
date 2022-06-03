#!/bin/bash
set -e

if [ "$1" = 'run' ]; then
    composer install --no-ansi --ignore-platform-reqs --no-interaction --no-progress --no-scripts --optimize-autoloader
    composer update laravel/framework
    php artisan migrate --force
    composer dump-autoload --optimize
    php artisan cache:clear
    php artisan config:clear
    php artisan shipments:store
    php-fpm
fi

exec "$@"
