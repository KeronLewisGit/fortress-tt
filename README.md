# Fortress Security Services – modern website & recruitment platform

A Laravel 13 rebuild of [fortress.tt](https://www.fortress.tt) with a careers page, online applications and a recruitment / administration back office. Content validated against the current site, the company Facebook page and business directory listings (October 2026).

## Features

**Public site** – home, about (history, mission, certifications, offices), services catalogue with detail pages, training ladder, careers with searchable vacancies, 5-minute application form with CV upload, quote request form, vehicle-tracking redirect, sitemap and JSON-LD.

**Imagery & motion** – 14 licensed Shutterstock photos (WebP, two sizes) and a looping control-room video in the hero, a CSS radar "live monitoring" mograph, kinetic headline, scroll-reveal, count-up stats and a credentials marquee, all respecting `prefers-reduced-motion`.

**Recruitment back office** (`/admin`) – dashboard with 14-day application chart and pipeline counts, job posting editor (draft/open/paused/closed, closing dates, urgent flag, duplicate), applicant list with filters and CSV export, drag-and-drop hiring pipeline, applicant profile with rating, recruiter assignment, interview scheduling, notes timeline, one-click templated emails that can advance the stage, secure CV download, quote-enquiry CRM, email templates, site settings, services editor, users with admin/recruiter roles.

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

```bash
scripts/package-hostinger.sh   # → dist/fortress-hostinger.zip
```

Follow `INSTALL-HOSTINGER.md` (included in the zip).
