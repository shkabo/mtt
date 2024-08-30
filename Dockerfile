FROM webdevops/php-nginx:8.3-alpine

ENV WEB_DOCUMENT_ROOT='/app/public'

COPY . /app

WORKDIR /app

RUN chown -R nginx:nginx /app

RUN composer install

EXPOSE 80 443
