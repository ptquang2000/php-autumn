FROM php:8.1.2-apache-bullseye
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN echo "session.save_path = \"/tmp\"" >> $PHP_INI_DIR/php.ini
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
EXPOSE 8080 8080
CMD a2enmod rewrite &&\
service apache2 restart &&\
php -S localhost:8080