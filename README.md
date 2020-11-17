# Perfecty Push WP Plugin

![CI](https://github.com/rwngallego/perfecty-push-wp/workflows/CI/badge.svg?branch=master)

Sef-hosted Push Notifications using your own server for free!

# Installation

Create the docker image:

```
docker build -t custom-wordpress:5.2.3-php7.2-apache .
```

In a fresh installation you need:

```
wp core install --url=localhost --title="Perfecty WP" --admin_user=admin --admin_password=admin --admin_email=info@perfecty.co --allow-root
wp plugin update --all --allow-root
```

Run the unit tests:

```
./bin/install-wp-tests.sh $WORDPRESS_DB_NAME $WORDPRESS_DB_USER $WORDPRESS_DB_PASSWORD $WORDPRESS_DB_HOST latest true
phpunit
```