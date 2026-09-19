# Lulu — Laravel Setup

This folder contains helper files to initialize a Laravel project.

Options

- Local (Composer installed):

```bash
# from inside the Lulu folder
composer create-project laravel/laravel . --prefer-dist
cp .env.example .env
composer install
php artisan key:generate
# configure DB settings in .env then run:
php artisan migrate
npm install
npm run dev
php artisan serve --host=0.0.0.0 --port=8000
```

- Docker (no Composer locally):

```bash
# start DB and web containers
docker-compose up -d db
# create project using the composer service
docker-compose run --rm composer create-project laravel/laravel . --prefer-dist
# bring up all services
docker-compose up -d
# generate key
docker-compose run --rm composer bash -lc "php artisan key:generate"
```

Notes

- App will be available at http://localhost:8000 when using Docker (nginx) or `php artisan serve`.
- Edit `.env` to match your DB credentials. Example in `.env.example`.
