# Fortress Security Services – website + recruitment platform

Laravel 13 / PHP ^8.3 / Tailwind v4 / Alpine.js. No Livewire, no Breeze. Must stay deployable to Hostinger shared hosting (PHP 8.3, no queue worker: `QUEUE_CONNECTION=sync`).

- Public pages: `PageController`, `CareerController`, `ContactController`; views in `resources/views/pages/`. Layouts in `resources/views/components/layouts/`.
- Static company copy (offices, training ladder, benefits, certifications) in `config/fortress.php`, read via `fortress('key')`. Editable settings are key/value in `settings` via `setting('key')` (`app/Support/helpers.php`); defaults in `SettingsSeeder`.
- Recruitment: `JobPosting` (status draft/open/paused/closed, `live()` scope), `Application` (pipeline stages in `Application::STAGES`, `moveTo()` writes timeline), `ApplicationNote`, `MessageTemplate` (placeholders rendered by `MessageTemplate::render`). Admin controllers in `app/Http/Controllers/Admin/`.
- CVs are stored on the `local` disk under `resumes/` and served only via `admin.applications.resume`.
- Roles: `admin` (everything) and `recruiter` (jobs, applicants, templates, enquiries). Middleware aliases `admin`, `active`.
- Kanban drag-and-drop + email composer live in `resources/js/app.js` (Alpine components `pipeline`, `composer`, `bars`).
- Local demo data: `php artisan db:seed --class=DemoSeeder` (admin@fortress.test / hr@fortress.test, password `password`).
- Package for Hostinger: `scripts/package-hostinger.sh` → `dist/fortress-hostinger.zip`; deploy files in `deploy/`.
- Run `php artisan test` after changes.
