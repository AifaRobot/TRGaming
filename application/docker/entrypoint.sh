#!/bin/sh
set -e

cd /var/www/html

if [ ! -d "vendor" ]; then
    echo ">>> Instalando dependencias de Composer..."
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

    echo ">>> Aplicando parche de compatibilidad Composer 2.x / Laravel 5.8..."
    php docker/patch-manifest.php

    echo ">>> Descubriendo paquetes..."
    php artisan package:discover --ansi
fi

chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

exec php-fpm
