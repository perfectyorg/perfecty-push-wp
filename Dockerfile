FROM wordpress:5.7-php7.3-apache

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
  && php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
  && php composer-setup.php \
  && php -r "unlink('composer-setup.php');" \
  && mv composer.phar /usr/local/bin/composer

# Install WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
  && chmod +x wp-cli.phar \
  && mv wp-cli.phar /usr/local/bin/wp

# Install phpunit
RUN wget https://phar.phpunit.de/phpunit-5.7.phar \
  && chmod +x phpunit-5.7.phar \
  && mv phpunit-5.7.phar /usr/local/bin/phpunit
