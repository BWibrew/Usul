.PHONY: clean build

all: build start

build: ## Build the entire environment
	@composer install --ignore-platform-reqs --no-suggest
	@yarn
	@yarn development
	@php -r "file_exists('.env') || copy('.env.example', '.env');"
	@docker-compose build --no-cache
	@docker-compose stop

start: ## Start docker containers
	@docker-compose up --abort-on-container-exit --no-recreate

down: ## Stop & Remove current containers and volumes
	@docker-compose down -v

clean: ## Clean all build process assets and artifacts
clean: down clean-images clean-vendor

clean-images: ## Clean any Docker images created by the build process
	@-docker images -a | grep "usul_" | awk '{print $$3}' | xargs docker rmi

clean-vendor: ## Clean any vendor files downloaded by the build process
	@-rm -rf ./node_modules
	@-rm -rf ./vendor
	@-rm -rf ./storage/app/public/*
	@-rm -rf ./storage/debugbar/*
	@-rm -rf ./storage/framework/cache/*
	@-rm -rf ./storage/framework/sessions/*
	@-rm -rf ./storage/framework/testing/*
	@-rm -rf ./storage/framework/views/*
	@-rm -rf ./storage/logs/*
