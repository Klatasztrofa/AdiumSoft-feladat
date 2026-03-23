FROM trafex/php-nginx:3.6.0 AS base

USER root

RUN apk add --no-cache \
    php83 \
    php83-common \
    php83-xdebug \
    php83-pdo \
    php83-pdo_mysql \
    composer \
    fish \
    vim

COPY supervisord.dev.conf   /etc/supervisord.conf
COPY php.ini                /etc/php83/php.ini
COPY vimrc                  /etc/vim/vimrc
COPY xdebug.ini             /etc/php83/conf.d/50_xdebug.ini
COPY config.fish            /tmp/fish/config.fish
COPY nginx-server.dev.conf  /etc/nginx/conf.d/default.conf

ENV XDG_CONFIG_HOME=/tmp
ENV HOME=/home/nobody
RUN <<SH
mkdir -p \
    /tmp/fish \
    /home/nobody

chown -R nobody:nobody \
    /tmp/fish \
    /home/nobody
SH

WORKDIR /var/www/html
EXPOSE 80
CMD ["supervisord", "--configuration", "/etc/supervisord.conf"]

USER nobody

