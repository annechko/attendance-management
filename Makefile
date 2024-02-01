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

init: docker-down docker-build docker-up app-init


bash:
	docker exec -it att-php-fpm /bin/sh

