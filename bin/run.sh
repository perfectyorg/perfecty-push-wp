#!/usr/bin/env bash

if [ $# -lt 1 ]; then
  echo "Run utilities"
  echo "----------------------"
	echo "  usage: $0 <command> [options]"
  echo "    <command> can be any of: up, down, console, setup, wordpress, deps, phpunit, sdk, test, format, bundle, svnsync"
  echo " .  [options]: --verbose"
	exit 1
fi

COMMAND=$1
VERBOSE=$2

PLUGIN_PATH="/var/www/html/wp-content/plugins/perfecty-push-wp"

compose_exec() {
  declare command=$1

  docker-compose exec -T wordpress /bin/bash -l -c "$command"
}

plugin_cmd() {
  declare command=$1

  echo "cd $PLUGIN_PATH && $command"
}

setup() {
  wordpress
  deps
  phpunit
  sdk
}

up() {
  docker-compose up -d
}

down() {
  docker-compose down
}

console() {
  docker-compose exec wordpress /bin/bash
}

wordpress() {
  CMD='wp core install --url=https://localhost --title="Perfecty WP" --admin_user=admin --admin_password=admin --admin_email=info@perfecty.co --allow-root &&
       wp plugin update --all --allow-root'
  compose_exec "$CMD"
}

deps() {
  CMD=$(plugin_cmd 'rm -rf vendor && composer install')
  compose_exec "$CMD"
}

phpunit() {
  CMD=$(plugin_cmd './bin/install-wp-tests.sh $WORDPRESS_DB_NAME $WORDPRESS_DB_USER $WORDPRESS_DB_PASSWORD $WORDPRESS_DB_HOST latest true')
  compose_exec "$CMD"
}

sdk() {
  git submodule update --init
  (cd public/js/perfecty-push-sdk/ && npm install)
  (cd public/js/perfecty-push-sdk/ && npm run build)
}

test() {
  CMD=$(plugin_cmd 'phpunit --debug')
  compose_exec "$CMD"
}

format() {
  CMD=$(plugin_cmd 'vendor/bin/phpcbf')
  compose_exec "$CMD"
}

DIST_PATH="dist"
SVN_PATH="$DIST_PATH/svn"
OUTPUT_PATH="$DIST_PATH/source"

create_sdk_dist() {
  sdk
  mv public/js/perfecty-push-sdk/dist /tmp/
  rm -rf public/js/perfecty-push-sdk
  mkdir -p public/js/perfecty-push-sdk
  mv /tmp/dist/ public/js/perfecty-push-sdk
}

create_dist() {
  create_sdk_dist
  rm -rf $DIST_PATH
  mkdir -p $SVN_PATH $OUTPUT_PATH
  cp -Rp admin includes languages lib public composer.json composer.lock index.php LICENSE.txt perfecty-push.php README.txt uninstall.php $OUTPUT_PATH
  (cd $OUTPUT_PATH && composer install --quiet --no-dev --optimize-autoloader)
  cp index.php $OUTPUT_PATH/vendor/
}

bundle() {
  create_dist
  if [[ -z "$ZIP_NAME" ]]; then
    OUTPUT_ZIP=perfecty-push-notifications.zip
  else
    OUTPUT_ZIP=$ZIP_NAME
  fi
  echo "Zip: dist/$OUTPUT_ZIP"
  (cd $OUTPUT_PATH && zip -q -r $OUTPUT_ZIP * && mv $OUTPUT_ZIP ./../)
}

svnsync() {
  create_dist
  svn co -q https://plugins.svn.wordpress.org/perfecty-push-notifications $SVN_PATH
  cp assets/* "$SVN_PATH/assets/"

  # we don't sync vendor if the lock file is the same
  shasum "$SVN_PATH/trunk/composer.lock"
  shasum "$OUTPUT_PATH/composer.lock"
  if [[ $(shasum "$SVN_PATH/trunk/composer.lock" | head -c 40) == $(shasum "$OUTPUT_PATH/composer.lock" | head -c 40) ]]; then
    rsync -q -av $OUTPUT_PATH/* $SVN_PATH/trunk --exclude vendor
    echo "## no differences in /vendor, similar lock files ##"
  else
    rsync -q -av $OUTPUT_PATH/* $SVN_PATH/trunk
  fi

  (cd $SVN_PATH && svn add --force . && svn diff && svn stat)
}

svnpush() {
  if (cd $SVN_PATH && svn status | grep -e ^?); then
    echo "There are changes not added to the SVN"
    exit 1
  fi

  if [ -z "$SVN_USERNAME" ]; then
    echo "You need to provide the username as SVN_USERNAME=myname"
    exit 1
  fi

  if [ -z "$SVN_PASSWORD" ]; then
    echo "You need to provide the username as SVN_PASSWORD=mypassword"
    exit 1
  fi

  if [ ! -d "$SVN_PATH/tags" ]; then
    echo "You need to run svnsync first"
    exit 1
  fi

  if [ ! -z "$SVN_TAG" ] && [ -d "$SVN_PATH/tags/$SVN_TAG" ]; then
    echo "The tag $SVN_TAG already exists"
    exit 1
  fi

  if [ ! -z "$SVN_TAG" ]; then
    cd $SVN_PATH && svn cp trunk tags/$SVN_TAG && svn ci -m "Version $SVN_TAG" --username $SVN_USERNAME --password $SVN_PASSWORD
  else
    cd $SVN_PATH && svn ci -m "Sync trunk" --username $SVN_USERNAME --password $SVN_PASSWORD
  fi
}

#----------------------------------------------

case $COMMAND in
  "up" | "down" | "setup" | "wordpress" | "deps" | "phpunit" | "sdk" | "test" | "format" | "console" | "bundle" | "svnsync" | "svnpush")
    if [[ $VERBOSE == '--verbose' ]]; then
      set -ex
    else
      echo "Executing command $COMMAND..."
    fi

    eval $COMMAND
    ;;
  *)
    echo "Command not supported: $COMMAND"
    ;;
esac