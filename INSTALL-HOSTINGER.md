# Fortress Security Services – Hostinger installation guide

`fortress-hostinger.zip` is a ready-to-run production build. Nothing needs compiling on the server: PHP dependencies and front-end assets are included.

```
public_html/
├── index.php  .htaccess  robots.txt  favicon.svg  setup.php  INSTALL-HOSTINGER.md
├── build/                       compiled CSS, JS and fonts
├── assets/                      licensed photos (WebP) and the hero video loop
└── fortress-app/                the Laravel application (blocked from the web by its own .htaccess)
    ├── .env.example             → copy to .env and fill in
    └── storage/app/private/resumes/   applicant CVs (never web-accessible)
```

Requirements: **PHP 8.3** (8.4 also works), a **MySQL database**, and ideally a plan with **SSH access**. Without SSH, use the one-time `setup.php` installer.

## 1. Prepare hPanel

1. **PHP version:** *Advanced → PHP Configuration* → select **PHP 8.3**. Extensions `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `ctype`, `fileinfo`, `bcmath`, `curl`, `zip` must be on (they are by default). Set `upload_max_filesize` and `post_max_size` to at least **8M** (CV uploads).
2. **Database:** *Databases → MySQL Databases* → create a database and user. Note the name, user and password.
3. **SSL:** *Security → SSL* → install the free certificate and turn on **Force HTTPS**.
4. **Mailbox:** *Emails* → create (or use) `recruitment@fortress.tt`. Note the password. Application confirmations and recruiter alerts are sent from it.

## 2. Upload

1. *Files → File Manager* → open `public_html/` and delete the old site files (back them up first).
2. Upload `fortress-hostinger.zip` into `public_html/`, right-click → **Extract** into `public_html/` itself (not a sub-folder). If a sub-folder was created, move its contents up one level.
3. Delete the zip. Make sure hidden files were extracted: `public_html/.htaccess` and `public_html/fortress-app/.htaccess` must both exist.

## 3. Configure `.env`

In `public_html/fortress-app/` copy `.env.example` to `.env` and edit:

| Key | Value |
|---|---|
| `APP_URL` | `https://www.fortress.tt` (match the domain exactly, with or without `www`) |
| `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | from step 1.2 (`DB_HOST=localhost`) |
| `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS` | the mailbox from step 1.4 (or `MAIL_MAILER=log` to disable email for now) |
| `SETUP_TOKEN` | a long random string, only if you will use `setup.php` (step 4B) |
| `ADMIN_EMAIL`, `ADMIN_PASSWORD` | optional: uncomment to have the seeder create the first administrator (skip `app:create-admin`) |

Leave `APP_KEY` empty (generated in step 4). Keep `APP_DEBUG=false`.

## 4. Initialise

### 4A. With SSH (recommended)

*Advanced → SSH Access* → enable, connect, then:

```bash
cd domains/fortress.tt/public_html/fortress-app
php artisan key:generate --force
php artisan migrate --force --seed       # creates tables, services, email templates and starter vacancies
php artisan app:create-admin             # first administrator (or set ADMIN_EMAIL / ADMIN_PASSWORD in .env before --seed)
php artisan optimize
rm ../setup.php
```

If `php` is an old version, use the path shown in hPanel, e.g. `/opt/alt/php83/usr/bin/php artisan …`.

### 4B. Without SSH

1. Set `SETUP_TOKEN` in `.env`.
2. Visit `https://www.fortress.tt/setup.php?token=YOUR_SETUP_TOKEN`. It generates the key, runs migrations and seeds, then asks for the first administrator's details.
3. It deletes itself on success. Confirm `public_html/setup.php` is gone, then remove `SETUP_TOKEN` from `.env`.

## 5. Cron job (auto-close expired vacancies)

*Advanced → Cron Jobs* → add, every minute (Laravel decides what runs):

```
/opt/alt/php83/usr/bin/php /home/USERNAME/domains/fortress.tt/public_html/fortress-app/artisan schedule:run >> /dev/null 2>&1
```

## 6. Verify

| Check | Expect |
|---|---|
| `https://www.fortress.tt/` | Home page over HTTPS with fonts and the blue shield logo |
| `https://www.fortress.tt/careers` | Vacancies listed; open one and submit a test application with a PDF |
| `https://www.fortress.tt/login` | Sign in with the administrator you created; the applicant appears under *Applicants* |
| `https://www.fortress.tt/fortress-app/.env` | **Must be 403/404** |
| Test application | Confirmation email received; alert received at the recruitment address |

## 7. After installation

1. **Admin → Settings:** confirm phones, emails, WhatsApp number, social links and which addresses receive alerts.
2. **Admin → Jobs:** review the seven starter vacancies (based on your recent Facebook hiring posts). Edit, close or delete any that are not current; post new ones.
3. **Admin → Email templates:** adjust the interview, documents, offer and rejection wording.
4. **Admin → Users:** add recruiters. *Recruiter* accounts manage jobs, applicants, templates and enquiries; *Administrator* accounts also manage services, settings, users and can delete applicants.
5. **Admin → Interviews:** publish interview slots so applicants invited to interview can self-book from their portal (`https://www.fortress.tt/portal`).
6. Update the Facebook “We're hiring” posts to link to `https://www.fortress.tt/careers` and submit `https://www.fortress.tt/sitemap.xml` in Google Search Console.

## Updating later

Upload a new package the same way but **keep** `fortress-app/.env` and `fortress-app/storage/` (it holds applicant CVs). Then run `php artisan migrate --force && php artisan optimize` (SSH) or re-run `setup.php` with a token. Keep regular MySQL backups (*Files → Backups*): applicant data lives there.

## Troubleshooting

- **500 right after upload:** `.env` missing or `APP_KEY` empty → steps 3–4.
- **403 on the whole site:** `public_html/.htaccess` missing (hidden file not extracted). Re-upload it from the zip.
- **CSS/JS not loading:** `public_html/build/` must exist and `APP_URL` must use `https://` and the exact domain.
- **CV upload fails:** raise `upload_max_filesize` / `post_max_size` in PHP Configuration.
- **No emails:** check `MAIL_*` in `.env` and the notification addresses in Settings; errors are in `fortress-app/storage/logs/`.
- **Wrong times:** `APP_TIMEZONE` should stay `America/Port_of_Spain`.
