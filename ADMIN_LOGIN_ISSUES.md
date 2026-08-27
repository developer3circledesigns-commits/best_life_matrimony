# Login & Admin Auth — Logical Issues Report

> Scope: relationship between **User Login** (`login.php`) and **Admin access** in BestLife Matrimony.
> Date: 2026-08-27

## How login works today

- There is **one** login system (`login.php`). "Admin" is just an `is_admin = 1` flag on a `users` row — there is **no separate admin login page or admin session**.
- After login, `is_admin()` re-queries the DB each request (`includes/db.php:520`), so role changes apply immediately.
- Admin pages are gated by `require_admin()` inside `admin/_header.php` (included by all 7 admin pages) plus a direct `!is_admin()` check in `admin/admin_api.php:6`.

## Issues

### L1 — No role-based redirect after login
- **Where:** `login.php:55`
- **Problem:** Every successful login redirects to `./profile.php`, even for admins. An admin never lands on `/admin`; they must know to open *Account ▾ → Admin Panel*. The "admin login" experience is identical to a user's.
- **Fix:** After a successful login, `if (is_admin()) header('Location: ./admin/index.php');` (or surface an "Admin Dashboard" CTA on the profile page).

### L2 — Admin is unreachable on mobile *(confirmed bug)*
- **Where:** `includes/navbar.php:67-71` (desktop dropdown, `hidden sm:inline-flex`) and `includes/navbar.php:106-117` (mobile menu, no Admin Panel link)
- **Problem:** The only Admin Panel link lives in the desktop Account dropdown, which is hidden on mobile. The mobile menu's logged-in block lists Profile/Network/Messages/Verify/Password/Delete/About/Logout but **no Admin Panel**. An admin on a phone/tablet cannot reach the admin area at all.
- **Fix:** Add an `<?php if (is_admin()): ?>` Admin Panel link inside the mobile menu's logged-in block.

### L3 — A suspended admin is permanently locked out (no break-glass)
- **Where:** `login.php:36`
- **Problem:** Login blocks *any* `is_suspended` user, including admins, with no recovery path (admin uses the same login). If an admin is suspended — by mistake or by another admin — they cannot log in to fix it.
- **Fix:** Prevent suspending the last/sole admin; designate a non-suspendable super-admin; document the DB break-glass (`UPDATE users SET is_suspended = 0 WHERE id = ?`).

### L4 — Silent auto-promotion to admin during a normal user login
- **Where:** `login.php:44-47`
- **Problem:** If a logged-in user's email matches `$siteConfig['admin_emails']`, they are silently set `is_admin = 1` with no extra verification. A misconfigured/injected email list = instant privilege escalation, and it happens inside the ordinary user flow, blurring the user/admin boundary.
- **Fix:** Promote only via an explicit admin-bootstrap route (or require the account to be brand-new), keep `admin_emails` empty by default, and always log the action (already logged).

### L5 — "Remember me" is broken (session-stored token)
- **Where:** `includes/config.php:81-101` (`remember_me_set` / `remember_me_validate`)
- **Problem:** `remember_me_set()` stores the token hash in `$_SESSION` (`config.php:87`), but `remember_me_validate()` compares against that same session value. After the browser closes the session is gone, so the cookie never restores login. It also cannot be revoked server-side. Affects both user and admin login equally.
- **Fix:** Persist the hash in a `remember_tokens` DB table, validate the cookie against DB, re-establish the full session on match, and invalidate on logout / password change.

### L6 — Admin authorization is convention-based, not enforced
- **Where:** `admin/*.php` (all include `_header.php`); `admin/admin_api.php:6`
- **Problem:** All 7 admin pages happen to include `_header.php` (good) and the API checks `!is_admin()` (good), but there is no routing guard — a newly added admin page that forgets the include is silently unguarded.
- **Fix:** Enforce `require_admin()` at the top of every `admin/*.php`, or front all admin traffic through a single router.

### L7 — `require_admin()` gives no feedback / loses target
- **Where:** `includes/db.php:527`
- **Problem:** Redirects non-admins to `index.php` with no message and does not preserve the intended admin URL.
- **Fix:** Redirect to `login.php?redirect=admin/...` with a notice.

## Minor / by-design notes

- Admin accounts are not first-class: you can only promote an *existing* user (`admin/index.php:31` documents SQL/config promotion). There is no "create admin" UI.
- Admin and user share one session (`$_SESSION['user_id']` only). Acceptable for this app, but admin privileges ride on the same session as normal browsing.

## Recommended priority

| ID | Issue | Type | Priority |
|----|-------|------|----------|
| L2 | Mobile admin link missing | Bug | High |
| L5 | Remember-me broken | Bug | High |
| L1 | No role-based redirect | UX/Logic | Medium |
| L4 | Silent auto-promotion | Security | Medium |
| L3 | Suspended-admin lockout | Logic | Medium |
| L6 | Convention-based admin guard | Hardening | Low |
| L7 | No redirect feedback | UX | Low |

## Suggested implementation order

1. **L2** + **L1** — quick wins, improve admin discoverability & post-login flow.
2. **L4** — tighten admin bootstrap to avoid privilege escalation.
3. **L5** — rework remember-me to use a DB-backed token.
4. **L3 / L6 / L7** — hardening and recovery paths.

## Status — implemented 2026-08-27

| ID | Issue | Resolution | Location |
|----|-------|------------|----------|
| L1 | No role-based redirect | Admins now land on `./admin/index.php` after login; `?redirect=` from `require_admin()` is honoured (safe internal only). | `login.php:50-62` |
| L2 | Admin unreachable on mobile | Added `Admin Panel` link (guarded by `is_admin()`) to the mobile menu's logged-in block. | `includes/navbar.php:108` |
| L3 | Suspended-admin lockout | Block suspending/revoking yourself, and block suspending or revoking the **last** admin account. | `admin/admin_api.php` `suspend` & `revoke_admin` cases |
| L4 | Silent auto-promotion | Auto-promotion via `admin_emails` now only fires when **zero admins exist** (first-admin bootstrap). | `login.php:44-47` |
| L5 | Remember-me broken | Token hash now persisted in new `remember_tokens` table (schema v5), validated + rotated against DB, invalidated on logout and password change. Auto-login runs after `getDB()` is available. | `includes/config.php` `remember_me_*`, `includes/db.php` (table + auto-login), `SCHEMA_VERSION='v5'` |
| L6 | Convention-based admin guard | Already enforced: all 7 admin pages include `admin/_header.php` (calls `require_admin()` before output at line 3) and `admin_api.php` checks `!is_admin()` directly. Verified consistent. | `admin/_header.php:3`, `admin/admin_api.php:6` |
| L7 | No redirect feedback | `require_admin()` now preserves the target (`?redirect=`) and sets an `auth_flash` notice; `login.php` displays + clears it. | `includes/db.php:527`, `login.php:88-89` |

All modified files pass `php -l`. The `remember_tokens` table is auto-created on next web request (schema migrates v4 → v5).
