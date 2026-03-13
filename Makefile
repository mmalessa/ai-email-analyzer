DC = docker compose
APP = email-service
SH = bash

.DEFAULT_GOAL      = help

.PHONY: help
help:
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' Makefile | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

.PHONY: build
build:
	@BUILD_TARGET=build $(DC) build

### DEV
.PHONY: up
up: ## Start the project docker containers
	@$(DC) up -d

.PHONY: down
down: ## Down the docker containers
	@$(DC) down --timeout 25

.PHONY: sh
sh: ## Run shell in app container
	@$(DC) exec -it $(APP) $(SH)

.PONY: logs
logs:
	@$(DC) logs -f $(APP)

.PHONY: init
init: ## Init services
	@$(MAKE) init-php
	@$(MAKE) init-llm

.PHONY: init-php
init-php: ## Init php-based services
	@$(DC) exec -it email-service sh -c "composer install"

.PHONY: init-llm
init-llm: ## Init ollama
	@$(DC) exec ollama sh -c "ollama pull llama3.1:8b"
	@$(DC) exec ollama sh -c "ollama pull nomic-embed-text"
