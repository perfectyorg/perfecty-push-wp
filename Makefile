SHELL := /bin/bash

default:
	@echo "Make utility. Supports the following actions: "
	@echo "  up: Start the service containers"
	@echo "  down: Stop the service containers"
	@echo "  console: Remote console to the wordpress service container"
	@echo "  test: Run the unit tests"
	@echo "  format: Run the code beautifier"
	@echo "  wordpress: Setup wordpress and plugins"
	@echo "  composer: Install all the composer dependencies"
	@echo "  phpunit: Setup Wordpress as a testing environment for phpunit"
	@echo "  setup: Runs wordpress, composer and phpunit"
	@echo "  bundle: Builds the vendor in production mode and generates the distributable zip file (perfecty-push-notifications.zip)"

up:
	@./bin/run.sh up	
	@./bin/run.sh phpunit

down:
	@./bin/run.sh down

test:
	@./bin/run.sh test

format:
	@./bin/run.sh format

console:
	@./bin/run.sh console

wordpress:
	@./bin/run.sh wordpress

composer:
	@./bin/run.sh composer

phpunit:
	@./bin/run.sh phpunit

setup:
	@./bin/run.sh setup

bundle:
	@./bin/run.sh bundle
