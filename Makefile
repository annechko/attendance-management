docker-down:
	docker-compose down --remove-orphans
docker-build:
	docker-compose build
docker-up:
	docker-compose up -d --remove-orphans

app-init: app-composer-install app-migrations app-data

app-composer-install:
	docker exec -it att-php-fpm composer i

app-migrations:
	docker exec -it att-php-fpm php bin/console doctrine:migrations:migrate --no-interaction

app-data: app-users

app-users:
	docker exec -it att-php-fpm php bin/console app:create-admin
	docker exec -it att-php-fpm php bin/console app:create-teacher
	docker exec -it att-php-fpm php bin/console app:create-student

init: docker-down prod-docker-down docker-build docker-up app-init


bash:
	docker exec -it att-php-fpm /bin/sh

####### prod
prod-docker-down:
	docker-compose -f docker-compose-prod.yml down --remove-orphans

prod-build: prod-build-php prod-build-nginx prod-build-db

prod-build-php:
	docker --log-level=debug build --file=app/.docker/prod/php-fpm.docker --tag hub.docker.com:att-prod-php-fpm-0 app

prod-build-nginx:
	docker --log-level=debug build --file=app/.docker/prod/nginx.docker --tag hub.docker.com:att-prod-nginx-0 app

prod-build-db:
	docker --log-level=debug build --file=app/.docker/prod/db.docker --tag hub.docker.com:att-prod-db-0 app

prod-init: docker-down prod-docker-down prod-build prod-docker-up prod-app-init

prod-docker-up:
	docker-compose -f docker-compose-prod.yml up -d --remove-orphans

prod-app-init: prod-app-migrations prod-app-data

prod-app-migrations:
	docker exec -it att-prod-php-fpm php bin/console doctrine:migrations:migrate --no-interaction

prod-app-data:
	docker exec -it att-prod-php-fpm php bin/console app:create-admin
	docker exec -it att-prod-php-fpm php bin/console app:create-demo



