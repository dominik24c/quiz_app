# Quiz App
### 1. Development
#### 1.1 Install packages
```bash
symfony composer install
npm i
```
####1.4 Create .env.local and set environment variables.
```bash
touch .env.local
```
```dotenv
GOOGLE_RECAPTCHA_SITE_KEY="SITE_KEY"
GOOGLE_RECAPTCHA_SECRET="SECRET_KEY"
```
####1.3 Run server
```bash
symfony serve -d
npm run watch
```
#### 1.4 Run Database server
```bash
docker-compose up -d
```

#### 1.5 Create database(optional) and run migrations
```bash
symfony console doctrine:database:create
syfmony console doctrine:migrations:migrate
```

### 2. Tests
#### 2.1 Run database server and create database
```bash
docker-compose up -d
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test
```
#### 2.2 Run unit and integration test
```bash
./vendor/bin/phpunit
```
