# nfqtest
nfq restaurant list

Installation steps
=================

composer install
yarn install
yarn run encore dev

# make changes to .env configuration regarding database

php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load

# make sure .env IMAGE_REPOSITORY_PATH path is writable by apache 

login as admin@nfq.lt/the_new_password
