# Base API-Platform application

![workflow status](https://github.com/szelep/base-api-platform/actions/workflows/ci.yml/badge.svg)

With out of box configured JWT authentication.

## Setup (local instance)
1. create local config file `.env.local` and set `DATABASE_URL` to your postgres instance
2. run `php bin/console lexik:jwt:generate-keypair` to generate public&private JWT keys
3. run `composer install`
4. execute migrations `php bin/console doctrine:migrations:migrate`
5. (optional) execute fixtures `php bin/console doctrine:fixtures:load --purge-with-truncate`
6. run php server ex. `php -S 127.0.0.1:8000 -t './public'`


## Tests

You can use `phpunit` from vendor to run tests.

```
./vendor/phpunit/phpunit/phpunit ./tests --bootstrap=./tests/bootstrap.php --testdox 
```
