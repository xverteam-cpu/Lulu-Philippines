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
- Web sessions remain active for eight hours of inactivity by default (`SESSION_LIFETIME=480`). If a form is submitted with an expired CSRF token, the user is returned to the login page to refresh the session.

## Daily interest phone notifications

Users can enable browser push notifications from Profile → Notifications on each phone or browser. A notification is sent when daily interest is actually credited, including when a user opens the app and that visit triggers a catch-up credit. Notifications require HTTPS, a configured VAPID key pair, and the Laravel scheduler to be running. Users must grant permission; on iPhone/iPad, they must use iOS/iPadOS 16.4 or newer and open Lulu from its Home Screen icon.

Generate a VAPID key pair with `php artisan push:vapid`, then configure `VAPID_PUBLIC_KEY`, `VAPID_PRIVATE_KEY`, and `VAPID_SUBJECT` in the server environment. Keep the private key secret and use the same key pair across deployments. On Windows/XAMPP, if PHP reports that it cannot create an EC key, set `OPENSSL_CONF` to the installed `openssl.cnf` path in the same shell before running the command. Run `php artisan migrate --force` to add push subscriptions, and ensure the scheduler runs every minute (for example, with `* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1`) so the existing daily interest job can execute at 00:05. Users opt in separately on each device.

## Phone numbers

Signup and invited account setup require a mobile number. Existing accounts without a number are prompted to add one after login. SMS verification is temporarily disabled, so no Firebase or SMS-provider environment variables are needed. Phone numbers are collected but are not confirmed as belonging to the user.

Authenticated web activity records the latest public IP address. The admin view can show an approximate city, region, and country using a locally installed MaxMind GeoLite2 City database; user IPs are not sent to an external geolocation service. Download the database from [MaxMind GeoLite2](https://www.maxmind.com/en/geolite2/signup), place the `.mmdb` file outside the public web directory, and set `GEOIP_DATABASE_PATH` to its absolute path. The lookup refreshes when the IP changes or once per day. This approximate location is separate from the profile region entered by the user, and is not GPS location. It remains unavailable until the database is configured and may not precisely match a user's actual location.

The admin user details also show the approximate device category inferred from the browser's user-agent (for example, iPhone, Android phone, tablet, or computer). It cannot reliably identify every handset model. Last Seen timestamps are displayed in Manila time and refresh while the admin page is open; user activity heartbeats are sent every 30 seconds while a signed-in page is open.

Admins can create a single-use temporary password from a user's details when the user cannot access their email. The generated password is shown once in the admin dashboard; after using it, the user must choose a new password to complete sign-in.
