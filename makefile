docker-build:
	docker compose -f ./.docker/docker-compose.yaml build

docker-up:
	docker compose -f ./.docker/docker-compose.yaml up --build -d

docker-stop:
	docker compose -f ./.docker/docker-compose.yaml stop

docker-restart:
	docker compose -f ./.docker/docker-compose.yaml restart

docker-ps:
	docker compose -f ./.docker/docker-compose.yaml ps

docker-logs:
	docker compose -f ./.docker/docker-compose.yaml logs -f

php-console:
	docker compose -f ./.docker/docker-compose.yaml exec -it php-fpm /bin/bash

composer-install:
	docker compose -f ./.docker/docker-compose.yaml exec -it php-fpm composer install

cache-clear:
	docker compose -f ./.docker/docker-compose.yaml exec -it php-fpm ./bin/console cache:clear
	docker compose -f ./.docker/docker-compose.yaml exec -it php-fpm ./bin/console cache:clear --env=test --no-interaction

migrate:
	docker compose -f ./.docker/docker-compose.yaml exec -it php-fpm ./bin/console doctrine:migrations:migrate --no-interaction
	docker compose -f ./.docker/docker-compose.yaml exec -it php-fpm ./bin/console doctrine:migrations:migrate --env=test --no-interaction

mysql-console:
	docker compose -f ./.docker/docker-compose.yaml exec -it mysql mysql -u root -p"password"


#nodejs-logs:
	#docker compose -f ./.docker/docker-compose.yaml logs -f nodejs

#nodejs-console:
	#docker compose -f ./.docker/docker-compose.yaml exec -it nodejs /bin/sh

#translations:
#	docker compose -f ./.docker/docker-compose.yaml exec -it php-fpm ./bin/console app:translation:import ./var/translations.yaml
#	docker compose -f ./.docker/docker-compose.yaml exec -it php-fpm ./bin/console app:cache:translation:clear
