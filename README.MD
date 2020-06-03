# Symfony 4 + Docker demo project

Very modest demo project to demonstrate a very basic Symfony 4 webshop running in a bunch of Docker containers. This is not something you should want to run in production. It can however be used as a starting point.

## Remarks
- This is a demo project so it lacks _loads_ of features, intended for demonstration purposes only.
- Most messages are written in the Dutch language. The project currently has no i18n support.
- The contents of an unfinished order (your shopping cart) are being stored in a memcached instance for quick retrieval (The cart contents have to be loaded on every page load).
- By default two demo users are created, see `src/DataFixtures/UserFixtures.php`.
- The webshop contains no products by default. Use the admin interface to add some.
- composer can be run inside the php-fpm container, yarn should be run outside (on the Docker host system).

![Example image showing the admin interface](assets/img/prodmgmt.png)
![Example image showing the customer interface](assets/img/customer.png)

### Running (development time)

### Prerequisites
- Docker 
- Docker-compose
- Yarn

```
cp .env.mysql.example   .env.mysql                  # please edit this file to set secure secrets                  
cp .env.symfony.example .env.symfony                # please edit this file to set secure secrets

yarn install                                        # install frontend (dev)dependencies
yarn encode dev                                     # compile static frontend assets

docker-compose build                                # build all containers
docker-compose run --rm php-fpm composer install    # run composer install in a ephemeral container
docker-compose up -d mariadb                        # run mysql container in background to allow schema creation/seeding
docker-compose run --rm php-fpm \
    php bin/console doctrine:schema:update          # update (initial create) of the database schema
docker-compose run --rm php-fpm \
    php bin/console doctrine:fixtures:load          # seed database with test data
docker-compose up                                   # now run all containers in foreground
docker-compose rm -f                                # destroy all containers (cleaning up)
```

## Endpoints
- `localhost:8080` - Main web interface.
- `localhost:8033` - MariaDB (for administration, the application connects directly through the Docker network).