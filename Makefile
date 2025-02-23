DOCKER_ENV_FILE_PATH = docker/.env
DOCKER_ENV_LOCAL_FILE_PATH = docker/.env.local

ifeq ($(shell test -f ${DOCKER_ENV_LOCAL_FILE_PATH} && echo yes),yes)
    DOCKER_ENV_FILE_PATH = ${DOCKER_ENV_LOCAL_FILE_PATH}
endif

include ${DOCKER_ENV_FILE_PATH}

DOCKER_COMPOSE = docker compose --env-file ${DOCKER_ENV_FILE_PATH}
DOCKER_COMPOSE_PHP_EXEC = ${DOCKER_COMPOSE} exec php

##################
## Docker
##################

dc_build: #build services
	${DOCKER_COMPOSE} build

dc_up: #up containers
	${DOCKER_COMPOSE} up -d --remove-orphans

dc_network:
	docker network create -d bridge cw-smartlinks-shared-network

dc_rebuild_and_up: #stop, build services again and up them
	${DOCKER_COMPOSE} down --remove-orphans
	docker network rm cw-smartlinks-shared-network
	${DOCKER_COMPOSE} build
	docker network create -d bridge cw-smartlinks-shared-network
	${DOCKER_COMPOSE} up -d --remove-orphans

dc_ps: #show containers list
	${DOCKER_COMPOSE} ps -a

dc_down: #down containers
	${DOCKER_COMPOSE} down --remove-orphans

dc_enter_php: #enter php container
	${DOCKER_COMPOSE} exec php bash

dc_logs_php: #show php container logs
	${DOCKER_COMPOSE} logs php

##################
## Migrations
##################

db_diff: #generate new database migration
	${DOCKER_COMPOSE_PHP_EXEC} bin/console doctrine:migrations:diff --no-interaction

db_migrate: #execute database migrations
	${DOCKER_COMPOSE_PHP_EXEC} bin/console doctrine:migrations:migrate --no-interaction


##################
## CS-Fixer
##################

cs_check:
	${DOCKER_COMPOSE_PHP_EXEC} vendor/bin/php-cs-fixer fix --dry-run

cs_fix:
	${DOCKER_COMPOSE_PHP_EXEC} vendor/bin/php-cs-fixer fix


##################
## Cache
##################

cache_clear:
	${DOCKER_COMPOSE_PHP_EXEC} rm -Rf var/cache/*
	${DOCKER_COMPOSE_PHP_EXEC} bin/console cache:clear
	${DOCKER_COMPOSE_PHP_EXEC} bin/console cache:clear --env=test


##################
## Install dependecies via composer
##################

composer_install:
	${DOCKER_COMPOSE_PHP_EXEC} composer install -n


##################
## Analyze layers structure
##################

deptrac:
	vendor/bin/deptrac analyse

##################
## Analyze YAML files
##################

yamllint:
	yamllint .


##################
## PHPStan analysis
##################

phpstan:
	vendor/bin/phpstan analyse src