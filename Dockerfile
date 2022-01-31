FROM php:8.1.2-apache-bullseye
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN echo "session.save_path = \"/tmp\"" >> $PHP_INI_DIR/php.ini
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN a2enmod rewrite
RUN echo "ServerName php.autumn.demo" >> /etc/apache2/apache2.conf