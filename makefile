rebuild:
	@docker-compose up -d --build && docker-compose start

start:
	@docker-compose up -d && docker-compose start

stop:
	@docker-compose stop

restart: stop start

connect_app:
	@docker exec -it tiny_app bash

connect_nginx:
	@docker exec -it tiny_nginx bash

connect_redis:
	@docker exec -it tiny_redis bash

connect_mysql:
	@docker exec -it tiny_mysql bash

test:
	@docker-compose -f docker-compose.testing.yml run --rm app_testing php artisan test
