# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

**Sistem Informasi PMMBN** — a Laravel 13 / PHP 8.3 information system for a student organisation: public member self-registration (with email-OTP verification), an admin panel for curating members and content, KTA (member ID-card, "Kartu Tanda Anggota") generation with QR codes, and a CMS for articles/documents. UI text, enum labels, and code comments are in **Indonesian**; app config defaults to `en` with `id_ID` faker locale.

## Commands

```bash
composer setup        # one-time: install deps, copy .env, key:generate, migrate, npm install + build
composer dev          # run everything: php artisan serve + queue:listen + pail (logs) + vite, concurrently
npm run dev           # vite dev server only (HMR)
npm run build         # production frontend assets

composer test         # config:clear then php artisan test (PHPUnit)
php artisan test tests/Feature/LookupCollegesTest.php             # single file
php artisan test tests/Feature/LookupCollegesTest.php --filter=methodName   # single test
php artisan test --testsuite=Unit                                 # one suite (Unit | Feature)

./vendor/bin/pint     # format PHP (Laravel Pint, default ruleset — no pint.json)
```

Tests run against an **in-memory SQLite** DB (see `phpunit.xml`); local dev uses MySQL. Stack: Tailwind CSS 4 + Vite 8, **plain Blade** (no Livewire/Inertia/Vue).

## Architecture

### Two route files, one app
`bootstrap/app.php` registers `routes/web.php` (public site) normally, then in the `then:` callback loads `routes/web-admin.php` under the `admin/` URL prefix and `admin.` name prefix. **All admin routes therefore have an `admin.` name and live in `web-admin.php`** — don't add admin pages to `web.php`. Guests hitting `admin/*` are redirected to `admin.auth.login`.

### Authorization (Spatie laravel-permission)
Admin resource routes attach permissions per-action via `->middlewareFor('index', 'permission:members.view')` etc. — permissions are named `<resource>.<view|create|update|delete>`. `AppServiceProvider::boot()` registers a `Gate::before` that **auto-grants everything to users with the `Administrator` role**. `ArticlePolicy` adds finer-grained article rules. Auth is session-based via the single `users` table (admins only; public visitors are never authenticated).

### Domain: Member vs MemberActivation
This split is central. A **`MemberActivation`** is a *pending* public submission; a **`Member`** is the *verified* record. Flow:
1. Public form at `/about/member-activation` requires an OTP-verified email first (`MemberActivationEmailOtpVerification`, `verified_at`), then creates a `MemberActivation` (status PENDING) plus uploaded supporting documents.
2. Every state change is logged in `MemberActivationStatusLog` (status_id: 1 PENDING, 2 VERIFIED, 3 REJECTED); the model exposes `currentStatus` (latest log).
3. Admin reviews under `admin/member-activations`, can fetch a fuzzy `suggestion-member` match, then `accept` (creates the `Member` + its `Kta`) or `reject`.
4. `Kta` auto-generates a unique yearly-sequenced `number` on create; the card is viewable/printable at the public `kta/{ktaNumber}` route (PDF via `spatie/laravel-pdf` + dompdf, QR via `simple-qrcode`).

### Geographic data & lookups
`laravolt/indonesia` provides hierarchical `provinces → cities → districts → villages` (code-based keys). `LookupController` exposes public, rate-limited (`throttle:120,1`) **Select2-format** JSON at `/select/{cities,colleges,districts,villages}` (`{results, pagination:{more}}`), each filtered by its parent code + case-insensitive `q`. Frontend cascading selects depend on these.

## Conventions

- **Models use the Laravel 13 `#[Fillable([...])]` attribute** (above the class), not `protected $fillable`. Casts go in a `protected function casts(): array` method.
- `SoftDeletes` + `Userstamps` (from `wildside/userstamps`, namespace `Mattiverse\Userstamps`) are used throughout; migrations call `$table->userstamps()` / `$table->userstampSoftDeletes()`. Enum-cast columns (e.g. `gender_id` → `App\Enums\Gender`).
- **Enums** (`app/Enums/`) are backed PHP enums with `label()` (Indonesian display text) and `badgeClass()` (Bootstrap CSS) helpers.
- **File uploads** use Spatie MediaLibrary: collection names are model constants (e.g. `Member::SUPPORTING_DOCUMENTS_COLLECTION`), with per-model static helpers for allowed MIME/extension lists and validation rules. Media is deleted via dedicated `…/media/{media}` admin routes.
- **Validation** lives in Form Requests (`app/Http/Requests/`); some expose a `validatedPersistable()` that strips validation-only fields (e.g. `province_code`, `supporting_documents`) before persisting.
- Pagination is **Bootstrap 5** (`Paginator::useBootstrapFive()`); list controllers use `->paginate(15)->withQueryString()`.
- Views: `resources/views/admin/` (admin panel, `admin/layouts/app.blade.php`), `resources/views/front/` (public site), `resources/views/pdf/` (KTA card). Static query helpers live in `app/Support/` (e.g. `ArticleGrid`).

## Admin UI (Sneat theme)

Admin pages follow the **Sneat / Bootstrap 5** admin template. Visual source of truth is `references/admin-template/` (gitignored, local-only) — read the relevant HTML there before designing a new page/section and translate it to Blade with the same classes instead of inventing patterns.

- Pages: `@extends('admin.layouts.app')`, body in `@section('content')`, tab title in `@section('title', '…')`.
- Components: `card` / `card-body`, `table-responsive`, `form-control` / `form-select` / `form-label`, `btn btn-primary`, `alert` for feedback. Static assets resolve from `public/assets/`.
- Sidebar links use `route('admin.…')`; active state via `request()->routeIs('admin.<name>.*')`. Never point a menu at the dashboard closure unless it's an intentional placeholder.
- Forms: `@csrf` on POST/DELETE; `@csrf` + `@method('PUT'|'PATCH')` for updates; `enctype="multipart/form-data"` for uploads; show `@error` per field.
- Long content defaults to a `<textarea>` **except** the admin article form, which uses the **Quill** editor (`resources/views/admin/articles/_form.blade.php`).

## Selects (Select2)

Use **Select2** for select inputs (CRUD forms, filters, multiselect) — don't ship a bare `<select class="form-select">` unless native is explicitly requested. Add the `select2` class alongside `form-select`; assets live under `public/assets/vendor/libs/select2/` and **jQuery must load first**. Initialise with `width: '100%'`, and set `dropdownParent` for selects inside modals so the dropdown isn't clipped. Reference: `public/assets/js/admin-article-form.js`. Cascading geographic/college selects are backed by the `/select/*` lookup endpoints (see Architecture).

## Testing workflow (mandatory — Cursor rule `alwaysApply`)

After implementing or changing **feature behavior** (HTTP, auth, validation, model rules, important jobs), **always run the relevant tests and fix failures before calling the task done.** Prefer the tightest scope (`php artisan test tests/Feature/Admin/ArticleCrudTest.php` or `--filter=test_name`); run the full suite only when needed.

- Missing a test file? Add one. **Feature** tests mirror the domain namespace (e.g. `Tests\Feature\Admin\…`) following `tests/Feature/Admin/ArticleCrudTest.php` (`RefreshDatabase`, guest vs `actingAs`, assert status/redirect/view). **Unit** tests for pure rules/helpers go in `tests/Unit/…` (e.g. `tests/Unit/QuillContentNotEmptyTest.php`).
- Minimum bar: at least one **success** scenario for the changed main path; add a **guest/unauthorized or validation-failure** scenario when a security gate or important validation is involved. Don't assert long HTML blobs. Don't touch CI or run `npm run prod` for this.
