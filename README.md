# Fortress Security Services – modern website & recruitment platform

A Laravel 13 rebuild of [fortress.tt](https://www.fortress.tt) with a careers page, online applications and a recruitment / administration back office. Content validated against the current site, the company Facebook page and business directory listings (October 2026).

## Features

**Public site** – home, about (history, mission, certifications, offices), services catalogue with detail pages, training ladder, careers with searchable vacancies, 5-minute application form with CV upload, quote request form, vehicle-tracking redirect, sitemap and JSON-LD.

**Imagery & motion** – 14 licensed Shutterstock photos (WebP, two sizes) and a looping control-room video in the hero, a CSS radar "live monitoring" mograph, kinetic headline, scroll-reveal, count-up stats and a credentials marquee, all respecting `prefers-reduced-motion`.

**Applicant portal** (`/portal`) – applicants register with the email they applied with (existing applications are linked automatically), track each application from Sent → In review → Interview → Decision, book an interview from slots the admin publishes or see the date the admin set, keep a CV on their profile, and receive email plus in-portal notifications at every step.

**Recruitment back office** (`/admin`) – dashboard with 14-day application chart and pipeline counts, job posting editor (draft/open/paused/closed, closing dates, urgent flag, duplicate), applicant list with filters and CSV export, drag-and-drop hiring pipeline, interview slots and schedule (`/admin/interviews`), accept / reject / interview decisions that notify the applicant, applicant profile with rating, recruiter assignment, interview scheduling, notes timeline, one-click templated emails that can advance the stage, secure CV download, quote-enquiry CRM, email templates, site settings, services editor, users with admin/recruiter roles.

## Local development

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan db:seed --class=DemoSeeder   # optional demo applicants + logins
npm run build                            # or: npm run dev
php artisan serve
```

Sign in at `/login` with `admin@fortress.test` / `password` (demo seed).

## Deploy to Hostinger

**Git (recommended):** connect the repository in hPanel (*Advanced → Git*, root directory `public_html`). The root `.htaccess` forwards requests to `public/`, `composer.lock` is pinned to PHP 8.3 and `public/build` is committed, so a push deploys everything. Run `npm run build` and commit before pushing CSS/JS changes. First-time setup (`.env`, migrations, admin) is in `INSTALL-HOSTINGER.md`.

**Zip:** `scripts/package-hostinger.sh` → `dist/fortress-hostinger.zip`, then follow `INSTALL-HOSTINGER.md`.
