<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"/>
  <img src="https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"/>
  <img src="https://img.shields.io/badge/Spatie_Permission-6.24-4F46E5?style=for-the-badge" alt="Spatie"/>
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License"/>
</p>

---

# 🔐 Laravel Multi-Guard Authentication & RBAC System

> **Production-oriented** multi-guard authentication with Role-Based Access Control for the admin panel — separate sessions for users and admins, Spatie permissions, policies, and gates.

---

## 📋 1. Project Overview

This project demonstrates a **multi-guard authentication** architecture with a **Role-Based Access Control (RBAC)** layer for the admin panel. It separates **front-end users** (web guard) and **administrators** (admin guard) with distinct session lifecycles, password reset flows, and email verification. Admin actions are authorized via **Spatie Laravel Permission** (roles and permissions), with **policies and gates** enforcing fine-grained access on resources such as user management.

| Aspect | Description |
|--------|-------------|
| **Front (Web)** | `User` model, session guard `web`, routes under `/front` and `auth.php` |
| **Back (Admin)** | `Admin` model, session guard `admin`, routes under `/back` and `adminAuth.php` |
| **RBAC** | Roles & permissions for admins only; User CRUD gated by permissions |

---

## 🏗️ 2. System Architecture

- **Two authentication boundaries**
  - **Web guard** → Session-based auth for front-end users (`User`, `users`). Routes: `/front`, `routes/auth.php`.
  - **Admin guard** → Session-based auth for back-office (`Admin`, `admins`). Routes: `/back`, `routes/adminAuth.php`.

- **Unified web middleware** → All web and back routes use the same `web` middleware group (session, CSRF, cookies, bindings). Guard selection happens at login and when resolving the current user (`Auth::guard('web')` vs `Auth::guard('admin')`).

- **Back-office RBAC** → Only the **Admin** model has roles/permissions. Permissions are scoped to the `admin` guard. User CRUD is gated by permissions (`add_user`, `show_user`, `edit_user`, `delete_user`) and **UserPolicy**.

- **API** → Minimal API group (`routes/api.php`) with Sanctum-protected `/api/user`.

---

## 🔑 3. Authentication Flow

### Front (web guard)

| Action | Route / Controller | Notes |
|--------|--------------------|--------|
| **Login** | `GET/POST /login` → `Auth\AuthenticatedSessionController` | Guest middleware → redirect to `front.index` |
| **Register** | `GET/POST /register` → `Auth\RegisteredUserController` | — |
| **Logout** | `POST /logout` | Guard `web` logged out → redirect `/` |
| **Password reset** | `PasswordResetLinkController`, `NewPasswordController` | `User` uses `UpdatedEmailNotification` → `/reset-password/{token}` |
| **Email verification** | `EmailVerificationPromptController`, `VerifyEmailController` | Signed, throttled |
| **Protected** | `/front` → `Front\HomeController` | `auth` middleware (web guard) |

### Back (admin guard)

| Action | Route / Controller | Notes |
|--------|--------------------|--------|
| **Login** | `GET/POST /back/login` → `AuthAdmin\AuthenticatedSessionController` | Guest → redirect `back.index` |
| **Register** | `GET/POST /back/register` → `AuthAdmin\RegisteredUserController` | — |
| **Logout** | `POST /back/logout` | Guard `admin` logged out → redirect `/back/login` |
| **Password reset** | `AuthAdmin\PasswordResetLinkController`, `NewPasswordController` | `Admin` uses `AdminPasswordNotification` → `/back/reset-password/{token}` |
| **Protected** | All `Route::prefix('back')` | **admin** middleware → unauthenticated redirect to `back.login` |

**RedirectIfAuthenticated** → Redirects to `back.index` when guard `admin` is authenticated, and to `front.index` when the default guard is authenticated.

---

## 🛡️ 4. Authorization Layer (RBAC)

- **Scope** → RBAC applies only to **admins**. **User** is the resource being managed (no roles/permissions).

- **Concepts**
  - **Permissions** → Abilities (`add_user`, `show_user`, `edit_user`, `delete_user`) in `permissions` with `guard_name = 'admin'`.
  - **Roles** → Groups in `roles` with `guard_name = 'admin'`; assigned to admins; permissions attached to roles.
  - **Guard** → All checks use the **admin** guard.

- **Enforcement**
  - **User CRUD** → `Back\UserController` uses `Gate::forUser($admin)->authorize('add_user'|'show_user'|'edit_user')` and **UserPolicy** for create/update/delete.
  - **SetPermission middleware** → `setPermission::permission_name` can restrict routes; defined but not applied on resource routes (explicit Gate/authorize in controller).
  - **Admins & Roles** → `Back\AdminController` (assign/sync roles), `Back\RolesController` (roles + permissions via `givePermissionTo` / `syncPermissions`).

---

## ⚙️ 5. Guards & Middleware Structure

### Guards (`config/auth.php`)

| Guard | Driver | Provider | Model |
|-------|--------|----------|--------|
| `web` | session | users | `App\Models\User` |
| `admin` | session | admins | `App\Models\Admin` |

**Password reset** → Separate `passwords` entries for `users` and `admins` (same table, same expire/throttle).

### Middleware (`app/Http/Kernel.php`)

| Alias | Class | Purpose |
|-------|--------|---------|
| `auth` | `Authenticate` | Redirect to `login` or `back.login` for `back/*` |
| `guest` | `RedirectIfAuthenticated` | Redirect authenticated admin → `back.index`, web → `front.index` |
| `admin` | `Admin` | Require `Auth::guard('admin')->check()` else `back.login` |
| `setPermission` | `SetPermission` | Require admin has given permission else `403` |
| `can` | `Authorize` | Policy/gate authorization |
| `verified` | `EnsureEmailIsVerified` | — |

**Groups** → `web` (cookies, session, CSRF, bindings), `api` (throttle, bindings).

---

## 👥 6. Roles & Permissions Structure

- **Spatie Laravel Permission** → `Role` and `Permission` models; guard `admin` for both.
- **PermissionSeeder** → Seeds `add_user`, `show_user`, `edit_user`, `delete_user` with `guard_name = 'admin'`. Run to bootstrap permissions.
- **Admin model** → Uses `HasRoles`; admins have roles and inherit (or have direct) permissions.
- **Helper** → `permission($permission)` in `app/Helpers/helpers.php` returns whether the current admin has the given permission.

---

## 📜 7. Policies & Gates Implementation

- **UserPolicy** → Registered for `User::class`. Methods `create`, `update`, `delete` receive **Admin** and use `hasAnyPermission('add_user'|'edit_user'|'delete_user')` → `Response::allow()` or `Response::deny(...)`.
- **Admin / Role / Permission** → No policies; protected by **admin** middleware only.
- **Gates** → Spatie registers a permission-check gate per permission (`register_permission_check_method` in `config/permission.php`). Custom Gate definitions in `AuthServiceProvider` are commented out; app uses Spatie gates + **UserPolicy**.

---

## 🗄️ 8. Database Design Overview

| Table | Purpose |
|-------|---------|
| **users** | Web guard; `id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `timestamps` |
| **admins** | Admin guard + RBAC; same structure as `users` |
| **password_reset_tokens** | Shared by both reset flows (`email`, `token`, `created_at`) |
| **permissions** | Spatie: `id`, `name`, `guard_name`, `timestamps` |
| **roles** | Spatie: `id`, `name`, `guard_name`, `timestamps` |
| **model_has_roles** | Pivot: Admin ↔ Role |
| **role_has_permissions** | Pivot: Role ↔ Permission |
| **model_has_permissions** | Direct model–permission (optional) |
| **personal_access_tokens** | Sanctum (API) |
| **failed_jobs** | Queue failed jobs |

**Relationships** → Admins ↔ Roles via `model_has_roles`; Roles ↔ Permissions via `role_has_permissions`. Users are not in RBAC tables.

---

## 📦 9. Tech Stack

| Layer | Technology |
|-------|------------|
| Framework | Laravel 10.x |
| PHP | ^8.1 |
| Auth stack | Laravel Breeze (Blade) — controllers & routes |
| Multi-guard | Session guards `web`, `admin` |
| RBAC | Spatie Laravel Permission ^6.24 |
| API auth | Laravel Sanctum ^3.3 |
| Frontend | Blade; back office assets under `public/assets-back` |

---

## 🚀 10. Installation Guide

**1. Clone and install dependencies**

```bash
git clone <repository-url>
cd Authentication_and_Authorization_Course
composer install
```

**2. Environment**

```bash
cp .env.example .env
php artisan key:generate
```

Configure `.env`: `APP_URL`, `DB_*`, `MAIL_*` (SMTP/Mailtrap, etc.).

**3. Database**

```bash
php artisan migrate
php artisan db:seed --class=PermissionSeeder
```

Or add `PermissionSeeder` to `DatabaseSeeder` and run `php artisan db:seed`.

**4. Optional**

```bash
php artisan storage:link
php artisan config:clear
```

---

## 📖 11. Usage Instructions

- **Front** → `/` (welcome), `/login`, `/register`, `/forgot-password`. After login → `/front`.
- **Back** → `/back/login` → dashboard `/back`; CRUD at `/back/admins`, `/back/roles`, `/back/users`. Create roles, assign permissions, assign roles to admins. User CRUD is permission-gated.
- **API** → Sanctum auth; `GET /api/user` returns authenticated user with valid token.

---

## 🔒 12. Security Considerations

| Area | Implementation |
|------|-----------------|
| **Guards** | Separate guards/providers for user vs admin sessions |
| **CSRF** | All web forms protected by `web` middleware |
| **Passwords** | Hashed (Laravel cast); reset tokens with expiry and throttle |
| **Email verification** | `User` and `Admin` implement `MustVerifyEmail`; routes for web guard |
| **Authorization** | Permission gates + UserPolicy for back-office user actions |
| **Rate limiting** | Login throttle (e.g. 5 attempts); API throttle |
| **Spatie cache** | Permission cache; clear after role/permission changes if needed |

---

## 🎯 13. Project Purpose

This codebase is a **reference implementation** for:

- Multi-guard authentication (web vs admin) with separate login, logout, and password reset flows.
- RBAC with Spatie Laravel Permission scoped to the admin guard.
- Combining **policies** (resource-level) and **permission-based gates** in one app.
- Custom password-reset notifications (user vs admin) and guard-aware redirects.

Suitable for **portfolios** and as a base for apps that need a clear separation between end-users and back-office staff with role-based permissions.

---
