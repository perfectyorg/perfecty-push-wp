FROM wordpress:5.9-php7.3-apache

RUN apt-get update
RUN apt-get install -y vim subversion mariadb-client wget
RUN apt-get install -y libgmp-dev \
  && docker-php-ext-configure gmp \
  && docker-php-ext-install gmp

# Enable SSL
RUN apt-get install -y --no-install-recommends ssl-cert && \
    	rm -r /var/lib/apt/lists/* && \
    	a2enmod ssl && \
    	a2ensite default-ssl

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
  && php composer-setup.php \
  && php -r "unlink('composer-setup.php');" \
  && mv composer.phar /usr/local/bin/composer

# Install WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
  && chmod +x wp-cli.phar \
  && mv wp-cli.phar /usr/local/bin/wp
