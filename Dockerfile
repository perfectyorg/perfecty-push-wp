FROM wordpress:5.6-php7.2-apache

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
  && php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
  && php composer-setup.php \
  && php -r "unlink('composer-setup.php');" \
  && mv composer.phar /usr/local/bin/composer

# Install WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
  && chmod +x wp-cli.phar \
  && mv wp-cli.phar /usr/local/bin/wp
