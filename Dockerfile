FROM php:8-cli-alpine AS php

WORKDIR /var/www/html

COPY ./public /var/www/html

CMD php -S 0.0.0.0:80 -t /var/www/html

EXPOSE 80