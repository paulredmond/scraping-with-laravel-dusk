#!/bin/bash

# Defaults to an app server
role=${CONTAINER_ROLE:-app}

echo "Container role: $role"

if [ "$role" = "queue" ]; then
    cd /srv/app && php artisan config:cache
    # Run queue
    php artisan queue:work --verbose --tries=3 --timeout=90
elif [ "$role" = "app" ]; then
    cd /srv/app && php artisan config:cache
    /usr/bin/caddy --agree=true --conf=/etc/Caddyfile
else
    echo "Could not match the container role...."
    exit 1
fi
