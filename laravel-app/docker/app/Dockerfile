FROM php:7.1.6-fpm

# Install required packages
RUN apt-get update -y \
    && apt-get install -yqq --no-install-recommends \
      ca-certificates wget xvfb unzip zlib1g-dev \
    && rm -rf /var/lib/apt/lists/* /var/cache/apt/*

# Caddy and PHP Dependencies
RUN curl --silent --show-error --fail --location \
      --header "Accept: application/tar+gzip, application/x-gzip, application/octet-stream" -o - \
      "https://caddyserver.com/download/linux/amd64?plugins=http.expires,http.git,http.realip" \
    | tar --no-same-owner -C /usr/bin/ -xz caddy \
    && chmod 0755 /usr/bin/caddy \
    && /usr/bin/caddy -version \
    && docker-php-ext-install mbstring pdo pdo_mysql zip

# Copy application code
COPY docker/app/php.ini /usr/local/etc/php/php.ini
COPY . /srv/app
COPY docker/app/entrypoint.sh /opt/bin/entrypoint.sh
COPY docker/app/Caddyfile /etc/Caddyfile

WORKDIR /srv/app

# Required folders and permissions
RUN chmod +x /opt/bin/entrypoint.sh \
    && mkdir -p /srv/app/bootstrap/cache \
    && mkdir -p /srv/app/storage/app/public \
    && mkdir -p /srv/app/storage/app/reports \
    && mkdir -p /srv/app/storage/framework/cache \
    && mkdir -p /srv/app/storage/framework/sessions \
    && mkdir -p /srv/app/storage/framework/views \
    && mkdir -p /srv/app/storage/logs \
    && touch /srv/app/storage/logs/laravel.log \
    && chown -R www-data:www-data /srv/app

EXPOSE 80 443 2015

CMD ["/opt/bin/entrypoint.sh"]
