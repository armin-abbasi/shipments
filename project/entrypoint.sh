#!/bin/bash
set -e

if [ "$1" = 'run' ]; then
    cp .env.example .env
    php aritsan key:generate
    php artisan migrate --force
    composer dump-autoload --optimize
    php artisan cache:clear
    php artisan config:clear
    php-fpm
fi

exec "$@"
