# Perfecty Push WP Plugin ⚡️

[![tests](https://github.com/rwngallego/perfecty-push-wp/workflows/tests/badge.svg)](https://github.com/rwngallego/perfecty-push-wp/actions)
[![License](https://img.shields.io/badge/license-GLPv2-blue.svg)](./LICENSE.txt)

Self-hosted Push Notifications from your own Wordpress server for free! 🥳

**Perfecty Push WP** is a Wordpress plugin for **Perfecty Push**, an Open Source project that allows you to send Web Push notifications
directly from your own server: No hidden fees, no third-party dependencies and you
own your data. 👏

## Features ✨

- Open Source and self-hosted
- No third-party dependencies when self-hosted
- Offline browser notifications through [Push API](https://developer.mozilla.org/en-US/docs/Web/API/Push_API) (Safari is not supported yet)
- You retain and own the user authorization tokens
- WordPress plugin with a built-in Push Server based on [web-push-php](https://github.com/web-push-libs/web-push-php)
- Send notifications when publishing posts

## Requirements 🧩

- `PHP 7.2+`
- The `gmp` extension.

**Note**: The `gmp` extension is optional and recommended only
for better performance.

## Local development 👨🏻‍💻

To see it in action in your local development enviroment, you need a set of
services which Wordpress relies on. You start off by creating the docker image:

```
docker build -t custom-wordpress:5.6.0-php7.2-apache .
```

Then start all the services and run the setup:

```
make up
make setup
```

You can now go to http://localhost/wp-login.php > Plugins > Activate the
**Perfecty Push** plugin.

![Screenshot preview](https://github.com/rwngallego/perfecty-push-wp/raw/master/.github/assets/perfecty.gif "Preview")

## Available commands 👾

```
# start the service containers
make up

# stop de service containers
make down

# remote console
make console

# run the unit tests
make test

# run the formatter
make format

# setup all: make wordpress, make composer, make phpunit
make setup

# setup wordpress and plugins
make wordpress

# install all the composer dependencies
make composer

# setup wordpress as a testing environment for phpunit
make phpunit

# generates the redistributable bundle as perfecty-push-notifications.zip
make bundle
```

## Testing ✅

This project relies on automated tests as in the [Wordpress Core](https://make.wordpress.org/core/handbook/testing/automated-testing/writing-phpunit-tests/) guidelines.

Run all the test suites:

```
make test
```

Run a single test:

```
make console
cd wp-contents/plugins/perfecty-push/
phpunit --filter test_schedule_broadcast_async
```

## Troubleshooting

**Not intended for production:** In case the plugins cannot be installed on your local installation do:

```
make console
chown -R www-data wp-content
```

## License 👓

The WordPress Plugin is an Open Source project licensed under [GPL v2](./LICENSE.txt).

The bell icon is a Font Awesome icon, a [CC BY 4.0 License](https://creativecommons.org/licenses/by/4.0/).

## Collaborators 🔥

[<img alt="rwngallego" src="https://avatars3.githubusercontent.com/u/691521?s=460&u=ceab22655f55101b66f8e79ed08007e2f8034f34&v=4" width="117">](https://github.com/rwngallego) |
:---: |
[Rowinson Gallego](https://www.linkedin.com/in/rwngallego/) |

## Special Thanks

[<img alt="Jetbrains" src="https://github.com/rwngallego/perfecty-push-wp/raw/master/.github/assets/jetbrains-logo.svg" width="120">](https://www.jetbrains.com/?from=PerfectyPush)

Thanks to Jetbrains for supporting this Open Source project with their magnificent tools.