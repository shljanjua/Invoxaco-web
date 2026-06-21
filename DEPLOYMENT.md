# Deploying Invoxaco

Invoxaco is a plain PHP 8.3+ application (no Node.js build step) designed to run on shared hosting such as Hostinger, as well as any standard LAMP/LEMP stack.

## Requirements

- PHP 8.3 or higher
- PHP extensions: `pdo`, `pdo_mysql`, `mbstring`, `json`
- MySQL 5.7+ / MariaDB 10.3+
- Apache with `mod_rewrite` (or equivalent rewrite support on nginx)
- HTTPS certificate (Let's Encrypt is fine)

## 1. Upload the files

Upload the entire project (including `vendor/`, which is already committed so no Composer step is required on the server) to your hosting account.

Two supported layouts:

**Recommended:** Point your domain's document root directly at the `public/` folder. This is the cleanest and most secure setup since `app/`, `database/`, `routes/`, `storage/`, and `setup/` are never web-accessible.

**Fallback:** If your host only lets you serve from the account root (common on shared hosting control panels), upload the project as-is to that root. The included root-level `.htaccess` rewrites all requests to `public/` automatically and blocks direct access to internal folders.

## 2. Run the setup wizard

Visit `https://yourdomain.com/setup/install.php` in a browser (or just visit your domain — `public/index.php` will redirect you there automatically if no `.env` file exists yet).

The wizard will:

1. Check PHP version, required extensions, and folder write permissions.
2. Collect your MySQL connection details, site URL, and admin account credentials.
3. Create the database (if it doesn't already exist), import `database/schema.sql` and `database/seed.sql`, and create your single admin user.
4. Write the `.env` file for you.

**After installation completes, delete `setup/install.php` (or the whole `setup/` folder)** so it can't be run again or discovered by anyone else.

## 3. Configure SMTP and integrations

Log in with your admin account and visit:

- **Admin > SMTP Settings** — configure outbound email (Hostinger SMTP, or any provider).
- **Admin > Website Settings** — site name, contact info, Google Analytics / Facebook Pixel IDs, social links.
- **Admin > SEO Settings** — per-page meta titles/descriptions/canonical overrides.

## 4. File permissions

Ensure the web server user can write to:

- `storage/cache/`
- `storage/logs/`
- `storage/sessions/`
- `public/uploads/`

`chmod -R 755` is sufficient on most shared hosts; the web server user typically already owns these via the hosting control panel.

## 5. Cron jobs (optional)

No background workers are required for Phase 1. If/when scheduled tasks (e.g. subscription expiry checks, digest emails) are added, they can be wired up as a Hostinger cron job calling a PHP CLI script.

## Security notes

- Never commit a real `.env` file. The `.gitignore` already excludes it.
- Delete `setup/install.php` immediately after installing.
- Keep `APP_DEBUG=false` in production — it's the default in `.env.example`.
- The app sends security headers (CSP, HSTS, X-Frame-Options, etc.) automatically in production via `app/bootstrap.php`.
