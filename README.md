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

## SMS phone verification

The signup, invitation setup, and existing-account phone forms use Firebase Authentication to send and verify SMS codes. Accounts without a verified number are redirected to verification before accessing authenticated pages. Before enabling the flow:

1. Create or select a Firebase project and register a Web app.
2. In Firebase Console, enable **Authentication → Phone** and allow SMS to the Philippines in the SMS region policy.
3. Link a Google Cloud Billing account and upgrade the Firebase project to the **Blaze** pay-as-you-go plan. Phone-auth SMS is billed by Google; review current Identity Platform pricing and set billing alerts before launch.
4. Add the production domain and any development hostnames under Firebase Authentication's authorized domains. Configure API-key restrictions to permit Firebase Authentication requests from your site.
5. Add the Web app values to `.env`:

   ```dotenv
   FIREBASE_API_KEY=
   FIREBASE_AUTH_DOMAIN=
   FIREBASE_PROJECT_ID=
   FIREBASE_APP_ID=
   ```

6. Clear cached Laravel configuration and rebuild frontend assets:

   ```bash
   php artisan config:clear
   npm install
   npm run build
   ```

Phone numbers are stored in E.164 format (for example, `+639171234567`). The server validates the Firebase ID token with Google's Identity Toolkit before marking the number verified; browser-submitted confirmation alone is not trusted.
