include .env

install:
	@$(MAKE) -s down
	@$(MAKE) -s docker-build
	@$(MAKE) -s up

build: docker-build
restart: down up
up: docker-up
down: docker-down
ps:
	@docker ps

docker-up:
	@docker-compose -p ${INDEX} up -d
docker-down:
	@docker-compose -p ${INDEX} down --remove-orphans
docker-build: \
	docker-build-php-cli \
	docker-build-php-fpm \
	docker-build-nginx \
	docker-build-nodejs

docker-build-nginx:
	@docker-compose -p ${INDEX} build nginx --no-cache
docker-build-php-fpm:
	@docker-compose -p ${INDEX} build php-fpm --no-cache
docker-build-php-cli:
	@docker-compose -p ${INDEX} build php-cli --no-cache
docker-build-nodejs:
	@docker-compose -p ${INDEX} build nginx --no-cache

docker-logs:
	@docker-compose -p ${INDEX} logs -f
shell:
	docker-compose -p ${INDEX} run --rm php-cli /bin/sh
php-fpm:
	@docker exec -it php-fpm /bin/sh

chown:
	@$(MAKE) app-php-cli-exec cmd="chown 1000:1000 -R ./"

