# Laravel Multi-Guard Authentication & RBAC System

## 1. Project Title

**Laravel Multi-Guard Authentication and Role-Based Access Control (RBAC) System**

---

## 2. Project Overview

This project is a production-oriented Laravel application demonstrating a **multi-guard authentication** architecture with a **Role-Based Access Control (RBAC)** layer for the admin panel. It separates **front-end users** (web guard) and **administrators** (admin guard) with distinct session lifecycles, password reset flows, and email verification. Admin actions are authorized via **Spatie Laravel Permission** (roles and permissions), with **policies and gates** enforcing fine-grained access on resources such as user management.

---

## 3. System Architecture Explanation

- **Two authentication boundaries**
  - **Web guard**: session-based authentication for front-end users (`User` model, `users` table). Used for routes under `/front` and auth routes from `routes/auth.php`.
  - **Admin guard**: session-based authentication for back-office users (`Admin` model, `admins` table). Used for routes under `/back` and auth routes from `routes/adminAuth.php`.

- **Unified web middleware**: All web and back routes use the same `web` middleware group (session, CSRF, cookies, bindings). Guard selection is done at login and when resolving the current user (`Auth::guard('web')` vs `Auth::guard('admin')`).

- **Back-office RBAC**: Only the **Admin** model participates in RBAC. Admins have **roles**; roles have **permissions**. Permissions are scoped to the `admin` guard. User CRUD in the back office is gated by permissions (`add_user`, `show_user`, `edit_user`, `delete_user`) and by the **UserPolicy** (and optionally Spatie-registered gates).

- **API**: A minimal API route group exists (`routes/api.php`) with Sanctum-protected `/api/user` for the default web user.

---

## 4. Authentication Flow

- **Front (web guard)**
  - **Login**: `GET/POST /login` → `Auth\AuthenticatedSessionController` (guest middleware). Session regenerated after successful auth; redirect to `front.index` (`/front`).
  - **Register**: `GET/POST /register` → `Auth\RegisteredUserController`.
  - **Logout**: `POST /logout` → `Auth\AuthenticatedSessionController@destroy` (auth middleware), guard `web` logged out, redirect to `/`.
  - **Password reset**: `Auth\PasswordResetLinkController`, `Auth\NewPasswordController`; `User` uses custom `UpdatedEmailNotification` for reset mail (link to `/reset-password/{token}`).
  - **Email verification**: Optional flow via `EmailVerificationPromptController`, `VerifyEmailController`, `EmailVerificationNotificationController` (signed, throttled).
  - **Protected front**: `/front` → `Front\HomeController` behind `auth` middleware (web guard).

- **Back (admin guard)**
  - **Login**: `GET/POST /back/login` → `AuthAdmin\AuthenticatedSessionController` (guest). Authenticates using guard `admin`; redirect to `back.index` (`/back`).
  - **Register**: `GET/POST /back/register` → `AuthAdmin\RegisteredUserController`.
  - **Logout**: `POST /back/logout` → `AuthAdmin\AuthenticatedSessionController@destroy` (admin middleware), guard `admin` logged out, redirect to `/back/login`.
  - **Password reset**: `AuthAdmin\PasswordResetLinkController`, `AuthAdmin\NewPasswordController`; `Admin` uses `AdminPasswordNotification` for reset mail (link to `/back/reset-password/{token}`).
  - **Protected back**: All routes under `Route::prefix('back')` that need admin auth use the **admin** middleware; unauthenticated admin requests redirect to `back.login`.

- **RedirectIfAuthenticated**: For guest routes, redirects to `back.index` when guard `admin` is authenticated, and to `front.index` when the default guard is authenticated.

---

## 5. Authorization Layer (RBAC Explanation)

- **Scope**: RBAC applies only to **admins**. The **User** model has no roles/permissions; it is the **resource** being managed in the back office.

- **Concepts**
  - **Permissions**: Named abilities (e.g. `add_user`, `show_user`, `edit_user`, `delete_user`) stored in `permissions` with `guard_name = 'admin'`.
  - **Roles**: Named groups (e.g. Super Admin, Editor) stored in `roles` with `guard_name = 'admin'`. Roles are assigned to admins; permissions are attached to roles (and optionally directly to admins via Spatie).
  - **Guard**: All permission/role checks use the **admin** guard so that only the `Admin` model is authorized via RBAC.

- **Enforcement**
  - **User CRUD**: `Back\UserController` uses `Gate::forUser($admin)->authorize('add_user'|'show_user'|'edit_user')` (and policy for create/update/delete). Spatie’s `register_permission_check_method` registers gates for each permission, so these gates resolve to the admin’s permissions.
  - **UserPolicy**: Maps `User::class` to `UserPolicy`; methods `create`, `update`, `delete` receive the current **Admin** (when invoked in back context) and allow/deny based on `add_user`, `edit_user`, `delete_user` respectively.
  - **Optional middleware**: `SetPermission` middleware (`setPermission::permission_name`) can restrict routes by permission (e.g. `setPermission::add_user`); it is defined but not applied on the resource routes in favor of explicit Gate/authorize calls in the controller.

- **Admins & Roles**: `Back\AdminController` lists, creates, updates, and deletes admins and assigns/syncs roles via Spatie (`assignRole`, `syncRoles`). `Back\RolesController` manages roles and attaches permissions via `givePermissionTo` / `syncPermissions`.

---

## 6. Guards & Middleware Structure

**Guards** (`config/auth.php`)

| Guard  | Driver  | Provider | Model        |
|--------|---------|----------|--------------|
| `web`  | session | users    | `App\Models\User`  |
| `admin`| session | admins   | `App\Models\Admin` |

**Password reset config**: Separate entries under `passwords` for `users` and `admins` (same table `password_reset_tokens`, same expire/throttle).

**Middleware** (`app/Http/Kernel.php`)

- **Groups**: `web` (cookies, session, CSRF, bindings), `api` (throttle, bindings).
- **Aliases**:
  - `auth` → `Authenticate` (redirects to `login` or `back.login` for `back/*`).
  - `guest` → `RedirectIfAuthenticated` (redirects authenticated admin to `back.index`, authenticated web to `front.index`).
  - `admin` → `Admin`: ensures `Auth::guard('admin')->check()`; otherwise redirects to `back.login`.
  - `setPermission` → `SetPermission`: accepts a permission name; allows request if the admin has that permission, else `abort(403)`.
  - `can` → Laravel’s `Authorize` (for policy/gate).
  - `verified` → `EnsureEmailIsVerified`.
  - `signed`, `throttle`, `password.confirm`, etc.

---

## 7. Roles & Permissions Structure

- **Spatie Laravel Permission**: Models `Spatie\Permission\Models\Role` and `Spatie\Permission\Models\Permission`; guard `admin` for both.
- **Seeder**: `PermissionSeeder` seeds permissions: `add_user`, `show_user`, `edit_user`, `delete_user` with `guard_name = 'admin'`. Run it (e.g. from `DatabaseSeeder`) to bootstrap permissions.
- **Admin model**: Uses `HasRoles` trait; admins get roles and inherit permissions from roles (and can have direct permissions).
- **Custom helper**: `permission($permission)` in `app/Helpers/helpers.php` (loaded via composer autoload-dev) returns whether the current admin has the given permission.

---

## 8. Policies & Gates Implementation

- **Policies**
  - **UserPolicy**: Registered for `User::class`. Methods `create(Admin $admin)`, `update(Admin $admin, User $model)`, `delete(Admin $admin, User $model)` use `$admin->hasAnyPermission('add_user'|'edit_user'|'delete_user')` and return `Response::allow()` or `Response::deny(...)`.
  - No policy is registered for `Admin` or for Spatie’s `Role`/`Permission`; back-office access to those is protected by the **admin** middleware only.

- **Gates**
  - Spatie registers a permission-check gate for each permission when `register_permission_check_method` is true (`config/permission.php`). So `Gate::forUser($admin)->authorize('add_user')` (and similarly `show_user`, `edit_user`) are valid and enforce the admin’s permissions.
  - Custom Gate definitions in `AuthServiceProvider` (e.g. `add_user`, `edit_user`, `show_user`) are commented out; the application relies on Spatie’s registered gates and on **UserPolicy** for user resource actions.

---

## 9. Database Design Overview

- **users**: `id`, `name`, `email` (unique), `email_verified_at`, `password`, `remember_token`, `timestamps`. Used by guard `web`.
- **admins**: Same structure as `users`. Used by guard `admin` and for RBAC (HasRoles).
- **password_reset_tokens**: `email` (primary), `token`, `created_at`. Shared by both password reset flows (config distinguishes by provider).
- **Spatie permission tables** (migration `create_permission_tables`):
  - **permissions**: `id`, `name`, `guard_name`, `timestamps`; unique `(name, guard_name)`.
  - **roles**: `id`, `name`, `guard_name`, `timestamps`; unique `(name, guard_name)`.
  - **model_has_roles**: pivot `role_id`, `model_type`, `model_id` (links Admin to Role).
  - **role_has_permissions**: pivot `permission_id`, `role_id`.
  - **model_has_permissions**: pivot for direct model–permission assignment (optional).
- **personal_access_tokens**: Laravel Sanctum (for API).
- **failed_jobs**: Laravel queue failed jobs.

**Relationships**: Admins have many roles (and roles many admins) via `model_has_roles`; roles have many permissions (and vice versa) via `role_has_permissions`. Users have no role/permission tables; they are the target of admin actions.

---

## 10. Tech Stack

| Layer        | Technology |
|-------------|------------|
| Framework   | Laravel 10.x |
| PHP         | ^8.1 |
| Auth (stack)| Laravel Breeze (Blade) – controllers and routes structure |
| Multi-guard | Laravel session guards (`web`, `admin`) |
| RBAC        | Spatie Laravel Permission ^6.24 |
| API auth    | Laravel Sanctum ^3.3 |
| Frontend    | Blade views; back office uses custom assets (e.g. Argon-style assets under `public/assets-back`) |

---

## 11. Installation Guide

1. **Clone and install dependencies**
   ```bash
   git clone <repository-url>
   cd Authentication_and_Authorization_Course
   composer install
   ```

2. **Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Configure `.env`: `APP_URL`, database (`DB_*`), and mail (`MAIL_*` for SMTP/Mailtrap, etc.).

3. **Database**
   ```bash
   php artisan migrate
   ```
   Ensure `PermissionSeeder` has been run so permissions exist:
   ```bash
   php artisan db:seed --class=PermissionSeeder
   ```
   Or add `PermissionSeeder` to `DatabaseSeeder` and run `php artisan db:seed`.

4. **Optional**
   - Link storage: `php artisan storage:link`
   - Clear config/cache: `php artisan config:clear` (especially after changing `config/permission.php`)

---

## 12. Usage Instructions

- **Front**: Visit `/` for the welcome page; `/login`, `/register`, `/forgot-password` for auth. After login, access the front dashboard at `/front`.
- **Back**: Visit `/back/login` to sign in as an admin. After login, `/back` is the dashboard; `/back/admins`, `/back/roles`, `/back/users` for CRUD. Create roles and assign permissions via Roles CRUD; assign roles to admins via Admins CRUD. User CRUD is permission-gated (`add_user`, `show_user`, `edit_user`, `delete_user`).
- **API**: Use Sanctum for API auth; `/api/user` returns the authenticated user (default guard) when called with a valid Sanctum token.

---

## 13. Security Considerations

- **Guards**: Front and back use separate guards and providers to avoid mixing user and admin sessions and credentials.
- **CSRF**: All web forms are protected by the `web` middleware (VerifyCsrfToken).
- **Passwords**: Stored hashed (Laravel’s `hashed` cast); reset tokens have expiry and throttle configured in `config/auth.php`.
- **Email verification**: Both `User` and `Admin` implement `MustVerifyEmail`; verification routes are in place for the web guard.
- **Authorization**: Back-office user actions are authorized by permission checks (gates) and UserPolicy rather than only by being logged in as admin.
- **Rate limiting**: Login uses a throttle (e.g. 5 attempts) in `LoginRequest`; API uses the `api` throttle.
- **Spatie**: Permission cache is used; clear cache after changing roles/permissions if not using the package’s automatic invalidation.

---

## 14. Project Purpose

This codebase serves as a **reference implementation** for:

- Multi-guard authentication (web vs admin) in Laravel with separate login, logout, and password reset flows.
- RBAC with Spatie Laravel Permission scoped to the admin guard.
- Combining **policies** (for resource-level rules) and **permission-based gates** (for ability names) in the same application.
- Custom notifications for password reset (user vs admin) and guard-aware redirects in middleware.

It is suitable for portfolios and as a base for applications that require a clear separation between end-users and back-office staff with role-based permissions.

---

## 15. Author Section

**Project**: Laravel Multi-Guard Authentication & RBAC System  
**Repository**: Authentication and Authorization Course  
**License**: MIT (or as specified in the repository)

For questions or contributions, please open an issue or pull request in the repository.
