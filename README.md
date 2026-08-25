# Digital Star Consultants

Laravel application for Digital Star Consultants (Mbagala, Dar es Salaam) — an IT/internet cafe, printing & design, stationery, and tech consultancy business.

## What this app does

- **Public site** — customers browse services and submit a service request with no account or login required (`GET /services`, `POST /submit`). Each submission gets a tracking reference customers can use to check status (`GET /track/{reference}`) without exposing their phone/email.
- **Staff/admin panel** — session-authenticated, role-gated (`admin`, `ceo`, `gm`, `staff`) area for managing service categories, services, dynamic per-service form fields, and incoming submissions (assign, mark in-progress/completed/rejected, download uploaded files).
- **Dynamic forms** — each service defines its own set of fields (`ServiceField`) so, e.g., a passport service can require a National ID field that a printing service doesn't need. Validation rules live in `ServiceField::getValidationRules()` as the single source of truth for both the public submit endpoint and the admin panel.

## Roles

| Role  | Access |
|-------|--------|
| admin / ceo / gm | Full admin panel: categories, services, fields, submissions, users, reports |
| staff | Submissions assigned to them only |
| (public) | Browse services, submit a request, track by reference — no account |

Staff/admin accounts are created only by an authenticated admin/ceo/gm via `POST /admin/users` (`Admin\UserController::store`), which assigns a role, dispatches a welcome email with login instructions, and never returns the temp password in the API response. There is no public self-registration route.

## Local setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate

# configure DB in .env, then:
php artisan migrate
php artisan db:seed   # local/staging only — refuses to run when APP_ENV=production

npm run dev            # or: npm run build
php artisan serve
```

Seeded demo accounts (local/staging only — see `PRODUCTION_CHECKLIST.md` for why these must not exist in production):

| Email | Password | Role |
|-------|----------|------|
| admin@digitalstar.local | password | admin |
| ceo@digitalstar.local | password | ceo |
| gm@digitalstar.local | password | gm |
| staff1@digitalstar.local | password | staff |
| staff2@digitalstar.local | password | staff |

## Tests

```bash
php artisan test
```

Covers auth, public submission flow, public service pages, staff submission access scoping, and mass-assignment protection on the `User` model.

## Before deploying

See `PRODUCTION_CHECKLIST.md` — environment, security, performance, and data steps to run through before go-live. `AGENTS.md` has notes for AI coding agents working in this codebase.

## Stack

- PHP 8.3, Laravel 13
- MySQL (XAMPP locally)
- Vite + Tailwind for the asset pipeline
