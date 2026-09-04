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

#mysql-console:
#	docker compose -f ./.docker/docker-compose.yaml exec -it mysql psql -U postgres


#postgres-console:
#	docker compose -f ./.docker/docker-compose.yaml exec -it postgres psql -U postgres
#
#migrate:
#	docker compose -f ./.docker/docker-compose.yaml exec -it php-fpm ./bin/console doctrine:migrations:migrate --no-interaction
#	docker compose -f ./.docker/docker-compose.yaml exec -it php-fpm ./bin/console doctrine:migrations:migrate --env=test --no-interaction
#
#translations:
#	docker compose -f ./.docker/docker-compose.yaml exec -it php-fpm ./bin/console app:translation:import ./var/translations.yaml
#	docker compose -f ./.docker/docker-compose.yaml exec -it php-fpm ./bin/console app:cache:translation:clear
#
#fixtures:
#	docker compose -f ./.docker/docker-compose.yaml exec -it php-fpm ./bin/console doctrine:fixtures:load --purger=develop_purger --no-interaction
