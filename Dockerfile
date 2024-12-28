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
  && php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
  && php composer-setup.php \
  && php -r "unlink('composer-setup.php');" \
  && mv composer.phar /usr/local/bin/composer

# Install WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
  && chmod +x wp-cli.phar \
  && mv wp-cli.phar /usr/local/bin/wp
