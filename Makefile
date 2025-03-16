init:
	docker-compose up -d --build
	sleep 20
	docker-compose exec app composer create-project --prefer-dist laravel/laravel .
	docker-compose exec app bash -c "cd /var/www/html && cp .env.example .env && php artisan key:generate"

breeze:
	docker-compose exec app composer require laravel/breeze --dev
	docker-compose exec app php artisan breeze:install
	docker-compose exec node npm install
	docker-compose exec node npm run build

up:
	docker-compose up -d --build

down:
	docker-compose down

restart:
	docker-compose down && docker-compose up -d --build

app:
	docker exec -it mihara-tt-app bash