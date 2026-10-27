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
- The public app URL is https://www.luluph.com. For local HTTP development, set `APP_URL=http://localhost:8000`, clear `SESSION_DOMAIN`, and set `SESSION_SECURE_COOKIE=false` in `.env`.

## Phone numbers

Signup and invited account setup require a mobile number. Existing accounts without a number are prompted to add one after login. SMS verification is temporarily disabled, so no Firebase or SMS-provider environment variables are needed. Phone numbers are collected but are not confirmed as belonging to the user.

Authenticated web activity records the latest public IP address. The admin view can show an approximate city, region, and country using a locally installed MaxMind GeoLite2 City database; user IPs are not sent to an external geolocation service. Download the database from [MaxMind GeoLite2](https://www.maxmind.com/en/geolite2/signup), place the `.mmdb` file outside the public web directory, and set `GEOIP_DATABASE_PATH` to its absolute path. The lookup refreshes when the IP changes or once per day. This approximate location is separate from the profile region entered by the user, and is not GPS location. It remains unavailable until the database is configured and may not precisely match a user's actual location.

The admin user details also show the approximate device category inferred from the browser's user-agent (for example, iPhone, Android phone, tablet, or computer). It cannot reliably identify every handset model. Last Seen timestamps are displayed in Manila time and refresh while the admin page is open; user activity heartbeats are sent every 30 seconds while a signed-in page is open.
