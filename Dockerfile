FROM php:7.4-cli
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
RUN install-php-extensions gd xdebug zip json mbstring
COPY --from=composer /usr/bin/composer /usr/bin/composer


COPY xdebug.ini /usr/local/etc/php/conf.d
WORKDIR /project
RUN composer require vlucas/phpdotenv
EXPOSE 9003
