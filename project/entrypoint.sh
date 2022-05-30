#!/bin/bash
set -e

if [ "$1" = 'run' ]; then
#    php artisan migrate --force
#    php artisan passport:safe_install
    composer dump-autoload --optimize
    php artisan cache:clear
    php artisan config:clear
    php-fpm
fi

exec "$@"
