# Perfecty Push WP Plugin

![CI](https://github.com/rwngallego/perfecty-push-wp/workflows/CI/badge.svg?branch=master)

Sef-hosted Push Notifications using your own server for free!

## Installation

Create the docker image:

```
docker build -t custom-wordpress:5.2.3-php7.2-apache .
```

Start all the services and setup the environment:

```
docker-compose up -d
make setup_all
```

## Available commands

```
#remote console:
make console
```

## Testing

Run the unit tests:

```
make test
```
