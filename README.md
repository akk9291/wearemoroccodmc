# We Are Morocco — website

Website and admin for **We Are Morocco**, a destination management company in Marrakech: private journeys, incentives, events and ground handling.

- **Laravel 12** with Blade views; all page content is stored in MySQL.
- **Filament 3** admin at `/admin`, for content, leads, redirects and site settings. There are Admin, Editor and Sales roles.
- **Website forms** (inquiry drawer, quote builder, contact, newsletter) are stored as leads. Each one emails the atelier and sends the guest a confirmation.
- **SEO**: a meta, Open Graph and JSON-LD head on every page, `sitemap.xml`, `robots.txt`, a redirects table, and automatic 301s when a slug changes.
- **Speed**: a guest full-page cache, cached listings, responsive images (srcset, WebP variants for uploads), and `public/.htaccess` with compression and browser caching.

The front-end design is in `public/assets/arena.css` and `public/assets/arena.js`. Keep both files as they are. Form handling is in `public/assets/forms.js`.

## Local setup (Windows + Laragon)

Requirements: PHP 8.2+ (Laragon ships 8.3), Composer, MySQL 8.

```bash
composer install
cp .env.example .env            # then set DB_DATABASE=wearemorocco, DB_USERNAME=root, DB_PASSWORD= (empty on Laragon)
php artisan key:generate
php artisan migrate --seed      # creates the tables and the site content; set ADMIN_PASSWORD in .env first
php artisan storage:link
php artisan serve               # http://127.0.0.1:8000, admin at /admin
```

- **Admin login:** `admin@wearemorocco.local`, with the password from `ADMIN_PASSWORD`. If that is empty, the seeder prints a generated password.
- **Mail:** set `MAIL_MAILER=log` locally. Emails are then written to `storage/logs/laravel.log`.
- **Tests:** `php artisan test` runs on in-memory SQLite. Never run it while the config is cached (`php artisan optimize:clear` first).
- **Image variants:** `php artisan images:variants` creates WebP variants for images uploaded before that feature existed.

## Deploy: Hostinger shared hosting (hPanel)

1. **PHP and database:**
   - hPanel → Advanced → PHP Configuration: PHP **8.3**, with the extensions `pdo_mysql`, `gd`, `intl`, `zip`, `fileinfo` and `mbstring`.
   - hPanel → Databases: create a MySQL database and user, and note the name, user and password.
2. **SSH:**
   - hPanel → Advanced → SSH Access: enable SSH and add the deploy public key.
   - Connect with `ssh -i ~/.ssh/hostinger_wearemorocco -p <port> <user>@<host>`.
3. **Code:**
   - Clone this repository into `~/domains/<domain>/laravel`, outside `public_html`.
   - In that folder, run `composer install --no-dev --optimize-autoloader`. Composer 2 is preinstalled on Hostinger.
4. **Point the site at `public/`.** Replace `public_html` with a symlink:
   `rm -rf ~/domains/<domain>/public_html && ln -s ~/domains/<domain>/laravel/public ~/domains/<domain>/public_html`.
   `public/.htaccess` already handles rewrites, compression and caching.
5. **`.env`** (create it on the server, never commit it):
   - `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://<domain>`, then run `php artisan key:generate`.
   - The `DB_*` values from step 1.
   - `MAIL_MAILER=smtp` with the Hostinger mailbox: `MAIL_HOST=smtp.hostinger.com`, `MAIL_PORT=465`, `MAIL_SCHEME=smtps`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`.
   - `QUEUE_CONNECTION=database`.
   - `APP_ENV=production` is required for search engines: on any other environment `robots.txt` blocks everything.
6. **Database:**
   - For a fresh site: `php artisan migrate --force --seed`.
   - To carry over content edited locally: import a dump of the local database, then run `php artisan migrate --force`.
7. **Storage and caches:** `php artisan storage:link`, then `php artisan optimize`.
8. **Cron** (hPanel → Advanced → Cron Jobs, every minute). This sends the queued form emails:
   `cd ~/domains/<domain>/laravel && php artisan queue:work --stop-when-empty --max-time=50 >/dev/null 2>&1`
9. **After the site is live:**
   - In Admin → Site settings, set the lead notification email, a PNG/JPG logo and the default share image.
   - Submit `https://<domain>/sitemap.xml` in Google Search Console.

**Every later deploy:**

```bash
git pull && composer install --no-dev --optimize-autoloader && php artisan migrate --force && php artisan optimize && php artisan cache:clear
```

Run `cache:clear` after code changes: the page cache is only flushed automatically when content is edited.
