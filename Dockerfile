FROM ralphschindler/tripleshot:latest

ENV PATH $PATH:/tripleshot/vendor/bin:/tripleshot/node_modules/.bin:/tripleshot
ENV APP_NAME pop-webmail
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV APP_ENV production

EXPOSE 80

WORKDIR /tripleshot
COPY . /tripleshot

#RUN composer install && composer clear-cache && \
#  npm install && \
#  php artisan optimize && \
#  npm run production && rm -Rf node_modules
