FROM php:7.4-cli
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
RUN install-php-extensions gd xdebug imap
COPY xdebug.ini /usr/local/etc/php/conf.d
EXPOSE 9003
