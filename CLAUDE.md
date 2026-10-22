# Fortress Security Services – website + recruitment platform

Laravel 13 / PHP ^8.3 / Tailwind v4 / Alpine.js. No Livewire, no Breeze. Must stay deployable to Hostinger shared hosting (PHP 8.3, no queue worker: `QUEUE_CONNECTION=sync`).

- Public pages: `PageController`, `CareerController`, `ContactController`; views in `resources/views/pages/`. Layouts in `resources/views/components/layouts/`.
- Static company copy (offices, training ladder, benefits, certifications) in `config/fortress.php`, read via `fortress('key')`. Editable settings are key/value in `settings` via `setting('key')` (`app/Support/helpers.php`); defaults in `SettingsSeeder`.
- Recruitment: `JobPosting` (status draft/open/paused/closed, `live()` scope), `Application` (pipeline stages in `Application::STAGES`, `moveTo()` writes timeline), `ApplicationNote`, `MessageTemplate` (placeholders rendered by `MessageTemplate::render`). Admin controllers in `app/Http/Controllers/Admin/`.
- CVs are stored on the `local` disk under `resumes/` and served only via `admin.applications.resume`.
- Roles: `admin` (everything) and `recruiter` (jobs, applicants, templates, enquiries). Middleware aliases `admin`, `active`.
- Kanban drag-and-drop + email composer live in `resources/js/app.js` (Alpine components `pipeline`, `composer`, `bars`).
- Local demo data: `php artisan db:seed --class=DemoSeeder` (admin@fortress.test / hr@fortress.test, password `password`).
- Imagery: licensed Shutterstock photos (Standard License, Keron's team plan) as WebP in `public/assets/img/` (1920 + 900 widths via `img('name', 900)` helper, `x-photo` component) and the hero video loop in `public/assets/video/`. Service → image map in `config/fortress.php` (`service_images`). Shutterstock IDs: 2383721233, 1670211232, 1957555531, 2585889401, 2632904299, 2299595321, 2638186699, 2811441823, 2491498405, 1792303945, 2808758841, 2849504413, 1714286392, 2256729835; video 3638070985.
- Motion layer: `[data-reveal]`/`.reveal-clip` scroll reveal, `rotator`/`counter` Alpine components, `.radar` mograph, `.marquee`, all disabled under `prefers-reduced-motion` (see the Motion layer block in `resources/css/app.css` and `resources/js/app.js`).
- Package for Hostinger: `scripts/package-hostinger.sh` → `dist/fortress-hostinger.zip`; deploy files in `deploy/`.
- Run `php artisan test` after changes.
