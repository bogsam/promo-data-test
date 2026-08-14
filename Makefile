COMPOSE=docker compose
APP=app

# Load variables from .env
ifneq (,$(wildcard .env))
include .env
export
endif

.PHONY: up down app app-rebuild install composer-install migrate migrate-fresh test deptrac larastan pint rector qa ide-helper

up:
	$(COMPOSE) up -d --build --wait
	@echo
	@echo "Application is available at: http://localhost:$(HTTP_PORT)/"

install:
	$(COMPOSE) up -d --build --wait
	$(MAKE) composer-install
	$(MAKE) migrate-fresh
	@echo
	@echo "Application is available at: http://localhost:$(HTTP_PORT)/"

down:
	$(COMPOSE) down

app:
	$(COMPOSE) exec $(APP) bash

app-rebuild:
	$(COMPOSE) up -d --no-deps --build --wait $(APP)
	@echo
	@echo "Application is available at: http://localhost:$(HTTP_PORT)/"

composer-install:
ifeq ($(wildcard src/composer.json),)
	@echo "Skipping composer install: src/composer.json not found."
else
	$(COMPOSE) exec $(APP) composer install
endif

migrate:
ifeq ($(wildcard src/artisan),)
	@echo "Skipping migrations: src/artisan not found."
else
	$(COMPOSE) exec $(APP) php artisan migrate
endif

migrate-fresh:
	$(COMPOSE) exec $(APP) php artisan migrate:fresh --seed

test:
	$(COMPOSE) exec $(APP) php artisan test $(ARGS) $(if $(f),--filter=$(f),)

deptrac:
	$(COMPOSE) exec $(APP) vendor/bin/deptrac analyse --report-uncovered

larastan:
	$(COMPOSE) exec $(APP) ./vendor/bin/phpstan analyse --memory-limit=1G

pint:
	$(COMPOSE) exec $(APP) ./vendor/bin/pint

rector:
	$(COMPOSE) exec $(APP) ./vendor/bin/rector process --config=rector.php

qa:
	$(MAKE) pint
	$(MAKE) larastan
	$(MAKE) deptrac
	$(MAKE) test

ide-helper:
	$(COMPOSE) exec $(APP) php artisan ide-helper:generate
	$(COMPOSE) exec $(APP) php artisan ide-helper:models -RW
	$(COMPOSE) exec $(APP) php artisan ide-helper:meta
