FROM trafex/php-nginx:3.6.0 AS base

USER root

RUN apk add --no-cache \
    php83 \
    php83-common \
    php83-xdebug \
    php83-pdo \
    php83-pdo_mysql \
    nodejs \
    npm \
    vim \
    composer

COPY api/    /var/www/html/api
COPY client/ /tmp/client

WORKDIR /var/www/html/api
RUN <<SH
XDEBUG_MODE=off composer install
SH

# Fixen ide küldjük a kéréseket, így nem lesz CORS gond
ENV VITE_API_URL=http://localhost:8300/api/v1

WORKDIR /tmp/client
RUN <<SH
npm ci
npm run build
cp -r /tmp/client/dist /var/www/html/client
SH

COPY docker/supervisord.prod.conf  /etc/supervisord.conf
COPY docker/php.ini                /etc/php83/php.ini
COPY docker/xdebug.ini             /etc/php83/conf.d/50_xdebug.ini
COPY docker/nginx-server.prod.conf /etc/nginx/conf.d/default.conf

EXPOSE 8000
EXPOSE 8001

CMD ["supervisord", "--configuration", "/etc/supervisord.conf"]

USER nobody
