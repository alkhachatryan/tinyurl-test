# Installation
1. git clone git@github.com:alkhachatryan/tinyurl-test.git
2. cd tinyurl-test
3. sudo make start
4. sudo make connect_app 

In the app container run the following commands:
1. composer install
2. php artisan key:generate
3. php artisan jwt:secret
4. php artisan migrate --seed # WARNING: 100.000.000 records will be created
5. php artisan queue:work

The API is accessible at: http://localhost:8000

# Notes
1. I used MySQL for storing and indexing DB, as you use MySQL.
2. Used indexes on product_categories and products tables. So GET /products endpoint takes ~200ms.
3. Used Redis for queueable jobs and there is one job \App\Jobs\AddProductToLastViewedProductsColumn for updating logged in users last viewed products.
4. There is auth flow, so users can login, register, logout, GET Me, forgot and reset passwords and verify an email.
5. POST, PUT, PATCH requests are under auth:api middleware, so user should be logged in for accessing that endpoints.
6. Assuming categories will be not be as much as products so I used all() method in my services for getting categories.
7. Used GlobalScope (with an index) for filtering deleted products. There is only one endpoint which can be called for deleted products - restore endpoint.
8. Included Postman Collection for easy testing my job.
9. I created a command for myself for checking the DB total size during seeding and indexing. Decided to leave it here.
10. Used DI services for users, tokens, products and categories resources.
11. The project is dockerized with docker-compose and makefile.

# Report for endpoints speed
These are the timing for most heavy queries. Tested with me PC with the following params:
1. RAM - 24GB
2. CPU - i5 9th gen
3. GPU - GeForce GTX 1650
4. OS Ubuntu - 22.04
5. Linux Kernel - 6.2.0-26-generic

And the timing is:
1. List all products (paginated ofc) with sorting (is_top with/without price&name) takes about 200ms
2. Getting single product with PK takes about 120ms
3. New product creation takes about 190ms
4. About 200ms takes product updating (also deleting and updating)
5. Category creation and updating take less than 100ms

### Important note
During indexing the MySQL engine was taking about 200GB from my SSD, then removing it. Looks like it was buffering existing data before creating an index and then removing useless data. Currently DB takes about 50GB in my storage (data + indexes)
