=== Perfecty Push Notifications ===
Contributors: rwngallego
Donate link: https://github.com/rwngallego
Tags: web push, push api, push notifications
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Sef-hosted Push Notifications using your own server for free!

== Description ==

Perfecty Push is an Open Source project that allows you to send push notifications
directly from your own server. No hidden fees, no third-party dependencies and you
own your data.

Advantages:

- Open Source and self-hosted
- No third-party dependencies when self-hosted
- Offline browser notifications through [Push API](https://developer.mozilla.org/en-US/docs/Web/API/Push_API) (Safari is not supported yet)
- You retain and own the user authorization tokens

Requirements:
- PHP 7.1+
- The gmp extension.

Note: In the roadmap we plan to support to PHP 7.2+, so the gmp extension will be optional
as PHP 7.2+ is enough.

== Installation ==

## Perfecty Push Server installation

1. Download the plugin

2. Install it and activate it

3. Go to the `Perfecty Push > Dashboard` section and configure it

4. You're ready to start sending notifications!

== Development ==

In a fresh installation you need:

```
wp core install --url=localhost --title="Perfecty WP" --admin_user=admin --admin_password=admin --admin_email=info@perfecty.co --allow-root
wp plugin update --all --allow-root
```

Run the unit tests:

```
./bin/install-wp-tests.sh $WORDPRESS_DB_NAME $WORDPRESS_DB_USER $WORDPRESS_DB_PASSWORD $WORDPRESS_DB_HOST latest true
vendor/bin/phpunit
```

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 1.0 =
* First version of the plugin with basic functionality

== Upgrade Notice ==

= 1.0 =
No comments