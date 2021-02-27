init: docker-down-clear \
	backend-clear frontend-clear websocket-clear parser-clear \
	docker-pull docker-build docker-up \
	backend-init frontend-init websocket-init
up: docker-up
down: docker-down
restart: down up
test: backend-test

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull --include-deps

docker-build:
	docker-compose build

permissions:
	cd ${PWD}/backend/src && sudo find . -type d -exec chmod a+rwx {} \; && sudo find . -type f -exec chmod a+rw {} \;
	cd ${PWD}/backend/migrations && sudo find . -type d -exec chmod a+rwx {} \; && sudo find . -type f -exec chmod a+rw {} \;
	cd ${PWD}/backend/config && sudo find . -type d -exec chmod a+rwx {} \; && sudo find . -type f -exec chmod a+rw {} \;
	cd ${PWD}/frontend/src && sudo find . -type d -exec chmod a+rwx {} \; && sudo find . -type f -exec chmod a+rw {} \;
	cd ${PWD}/websocket/src && sudo find . -type d -exec chmod a+rwx {} \; && sudo find . -type f -exec chmod a+rw {} \;

backend-clear:
	docker run --rm -v ${PWD}/backend:/app -w /app alpine sh -c 'rm -rf var/*'

backend-init: backend-permissions backend-install backend-wait-db backend-migrations

backend-permissions:
	docker run --rm -v ${PWD}/backend:/app -w /app alpine mkdir -p var/cache
	docker run --rm -v ${PWD}/backend:/app -w /app alpine mkdir -p var/log
	docker run --rm -v ${PWD}/backend:/app -w /app alpine chmod 777 var/cache var/log

backend-install:
	docker-compose run --rm backend-php-cli composer install
	docker-compose run --rm backend-node-cli yarn install
	docker-compose run --rm backend-node-cli yarn build

backend-wait-db:
	docker-compose run --rm backend-php-cli wait-for-it backend-db:5432 -t 30

backend-migrations:
	docker-compose run --rm backend-php-cli bin/console doctr:migr:migr

backend-validate-schema:
	docker-compose run --rm backend-php-cli bin/console doctr:schema:validate

backend-test:
	docker-compose run --rm backend-php-cli ./vendor/bin/phpunit tests

frontend-clear:
	docker run --rm -v ${PWD}/frontend:/app -w /app alpine sh -c 'rm -rf .ready dist'

frontend-init: frontend-install frontend-ready

frontend-install:
	docker-compose run --rm frontend-node-cli npm install

frontend-ready:
	docker run --rm -v ${PWD}/frontend:/app -w /app alpine touch .ready

frontend-lint:
	docker-compose run --rm frontend-node-cli npm run lint

frontend-eslint-fix:
	docker-compose run --rm frontend-node-cli yarn lint

frontend-pretty:
	docker-compose run --rm frontend-node-cli yarn lint

websocket-clear:
	docker run --rm -v ${PWD}/websocket:/app -w /app alpine sh -c 'rm -rf .ready'

websocket-init: websocket-ready

websocket-install:
	docker-compose run --rm websocket-go-cli go get -u github.com/gorilla/websocket

websocket-ready:
	docker run --rm -v ${PWD}/websocket:/app -w /app alpine touch .ready

ws:
	docker-compose up --build --force-recreate --no-deps -d websocket-go
	docker-compose logs --follow websocket-go
	
q:
	docker-compose up --build --force-recreate --no-deps -d backend-queue
	docker-compose logs --follow backend-queue

cron: 
	docker-compose run --rm backend-php-cli php bin/console schedule:run -v

i18n:
	docker-compose run --rm frontend-node-cli npm run i18n

i18n-merge:
	docker-compose run --rm frontend-node-cli xliffmerge --profile xliffmerge.json
	
parser-init: parser-install parser-ready

parser-install:
	docker-compose run --rm parser-node-cli sh -c 'cd tiktok && npm install'

parser-ready:
	docker run --rm -v ${PWD}/parser:/app -w /app alpine touch .ready

parser-clear:
	docker run --rm -v ${PWD}/parser:/app -w /app alpine sh -c 'rm -rf .ready'

parser:
	docker-compose up --build --force-recreate --no-deps -d parser parser-tiktok
