.PHONY: all
default: all;

env=dev

build:
	docker-compose \
		-f infrastructure/docker-compose.yml \
		-f infrastructure/docker-compose.$(env).yml \
		--project-directory $(CURDIR) \
		build

ci:
	docker-compose \
		-f infrastructure/docker-compose.yml \
		-f infrastructure/docker-compose.$(env).yml \
		-f infrastructure/docker-compose.ci.yml \
		--project-directory $(CURDIR) \
		build

run-ci:
	vendor/bin/phpstan analyse -l 8 src/
	vendor/bin/phpcs --colors --standard=ruleset.xml src/

build-prod:
	docker-compose \
		-f infrastructure/docker-compose.yml \
		-f infrastructure/docker-compose.prod.yml \
		--project-directory $(CURDIR) \
		build

start:
	docker-compose \
		-f infrastructure/docker-compose.yml \
		-f infrastructure/docker-compose.$(env).yml \
		--project-directory $(CURDIR) \
		up -d

install:
	docker-compose \
		-f infrastructure/docker-compose.yml \
		-f infrastructure/docker-compose.dev.yml \
		--project-directory $(CURDIR) exec php composer install

restart:
	docker-compose \
		-f infrastructure/docker-compose.yml \
		-f infrastructure/docker-compose.$(env).yml \
		--project-directory $(CURDIR) \
		restart

shell:
	docker-compose \
		-f infrastructure/docker-compose.yml \
		-f infrastructure/docker-compose.dev.yml \
		--project-directory $(CURDIR) exec php /bin/sh

logs:
	docker-compose \
		-f infrastructure/docker-compose.yml \
		-f infrastructure/docker-compose.$(env).yml \
		--project-directory $(CURDIR) \
		logs -f php

stop:
	docker-compose \
		-f infrastructure/docker-compose.yml \
		-f infrastructure/docker-compose.$(env).yml \
		--project-directory $(CURDIR) \
		down --remove-orphans --volumes
