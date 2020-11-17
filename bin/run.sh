#!/usr/bin/env bash

if [ $# -lt 1 ]; then
  echo "Run utilities"
  echo "----------------------"
	echo "  usage: $0 <command> [options]"
  echo "    <command> can be any of: setup_all, wordpress, composer, phpunit, test"
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

setup_all() {
  wordpress
  composer
  phpunit
}

wordpress() {
  CMD='wp core install --url=localhost --title="Perfecty WP" --admin_user=admin --admin_password=admin --admin_email=info@perfecty.co --allow-root &&
       wp plugin update --all --allow-root'
  compose_exec "$CMD"
}

composer() {
  CMD=$(plugin_cmd 'composer install')
  compose_exec "$CMD"
}

phpunit() {
  CMD=$(plugin_cmd './bin/install-wp-tests.sh $WORDPRESS_DB_NAME $WORDPRESS_DB_USER $WORDPRESS_DB_PASSWORD $WORDPRESS_DB_HOST latest true')
  compose_exec "$CMD"
}

test() {
  CMD=$(plugin_cmd 'phpunit')
  compose_exec "$CMD"
}

#----------------------------------------------

case $COMMAND in
  "setup_all" | "wordpress" | "composer" | "composer" | "phpunit" | "test")
    if [[ $VERBOSE == '--verbose' ]]; then
      set -ex
    else
      echo "Executing command..."
    fi

    eval $COMMAND
    ;;
  *)
    echo "Command not supported: $COMMAND"
    ;;
esac