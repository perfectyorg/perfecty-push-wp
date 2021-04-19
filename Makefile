SHELL := /bin/bash

default:
	@echo "Make utility. Supports the following actions: "
	@echo "  up: Start the service containers"
	@echo "  down: Stop the service containers"
	@echo "  console: Remote console to the wordpress service container"
	@echo "  test: Run the unit tests"
	@echo "  format: Run the code beautifier"
	@echo "  wordpress: Setup wordpress and plugins"
	@echo "  deps: Install all the composer dependencies"
	@echo "  phpunit: Setup Wordpress as a testing environment for phpunit"
	@echo "  sdk: Setup the JS SDK"
	@echo "  setup: Runs wordpress, composer, phpunit and sdk"
	@echo "  bundle: Builds the vendor in production mode and generates the distributable zip file (perfecty-push-notifications.zip)"
	@echo "  svnsync: Sync the current code with the SVN upstream repository"
	@echo "  svnpush: Creates a tag and push it to the SVN repository"

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

deps:
	@./bin/run.sh deps

phpunit:
	@./bin/run.sh phpunit

sdk:
	@./bin/run.sh sdk

setup:
	@./bin/run.sh setup

bundle:
	@./bin/run.sh bundle

svnsync:
	@./bin/run.sh svnsync

svnpush:
	@./bin/run.sh svnpush
