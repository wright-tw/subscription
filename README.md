# How to run?
```bash
git clone git@github.com:wright-tw/subscription.git
cd subscription

// make new .env file (maybe need change .env info)
cp .env.example .env

// install vendor
composer install

// create a empty database, name is "subscription"

// use php create table
php bin/hyperf.php migrate

// run service
php bin/hyperf.php start
```

# Env
```
php 8.0.15
swoole 4.8.6
mysql 5.7.32
```