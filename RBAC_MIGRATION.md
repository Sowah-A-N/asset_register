# RBAC & Folder Structure Migration Strategy

**Project:** RMU Asset Register v2 — Phase 3.5
**Status:** Strategy approved; Phase A foundation delivered (inert)
**Decisions (locked):** Quarantine legacy folders → `/_legacy` · Incremental (RBAC-layer-first) · Keep shared role-accounts for now

---

## 1. The Problem (verified, not assumed)

The current "role-based folders" are not six parallel asset-register implementations. Direct inspection shows three different applications mixed together:

| Folder | Actual content | Asset register? |
|--------|----------------|-----------------|
| `schedule_officer/` | 33 subdirs — the complete asset register | ✅ Canonical |
| `DSU/` | dashboard, reports, view_asset_location, sub_classes, archived_locations | ✅ Partial subset |
| `SIA/` | add_rooms, add_students, view_blacklist, view_pending_applications | ❌ Student accommodation app |
| `accountant/` | apply, pay_accommodation, student-register (+ dashboard shell) | ❌ Mostly accommodation |
| `budget_officer/` | apply, pay_accommodation, student-register | ❌ Accommodation, no dashboard |
| `director_finance/` | admin_dashboard, login only | ⚠️ Empty shell |

**Two authorization defects:**

1. **No real authorization.** Every protected page checks only `isset($_SESSION['username'])` — authentication, not authorization. Any logged-in user can open any role's folder. A DSU login can reach `schedule_officer/new_asset/` unhindered.
2. **Folders imply roles but enforce nothing.** The `admin_logs.user_role` column exists but is never read. Access is decided by which URL you navigate to, not by who you are.

Plus: `admin_logs` holds **6 shared role-accounts** (one generic "Schedule Officer" login, etc.), not individual people. Today you log in *as a role*, not *as a person who has roles*.

---

## 2. Target Model

Permissions — not folders, not role names — become the unit of access control.

```
users  (= existing admin_logs, unchanged)
  table_id · username · user_role · user_password

roles
  role_id · role_key · role_name · description

permissions
  permission_id · permission_key · module · description

role_permissions   (role_id ↔ permission_id)   — what each role may do
user_roles         (user_id ↔ role_id)         — multiple roles per user
```

`user_roles.user_id` references `admin_logs.table_id`. **`admin_logs` is never altered** — backward compatibility is preserved by addition, not modification.

### Roles (seeded from existing 6 + a future admin)

| role_key | maps from `admin_logs.user_role` |
|----------|----------------------------------|
| `schedule_officer` | `S/O` |
| `dsu` | `DSU` |
| `accountant` | `Accountant` |
| `budget_officer` | `B/O` |
| `sia` | `SIA` |
| `director_finance` | `D/F` |
| `system_admin` | *(new — RBAC management, no user yet)* |

### Permissions (by module)

| Module | Permissions |
|--------|-------------|
| assets | `asset.view` `asset.create` `asset.edit` `asset.move` `asset.archive` `asset.dispose` |
| catalog | `catalog.view` `catalog.manage` (classes, sub-classes, locations, types) |
| suppliers | `supplier.view` `supplier.manage` |
| people | `assetuser.view` `assetuser.manage` |
| rate | `rate.view` `rate.set` |
| reports | `report.view` `report.export` |
| admin | `user.manage` `role.manage` |

### Role → permission matrix (seeded)

| Permission | sched | dsu | acct | budget | sia | dir_fin | admin |
|-----------|:----:|:---:|:----:|:------:|:---:|:-------:|:-----:|
| asset.view      | ● | ● | ● | ● | ● | ● | ● |
| asset.create    | ● |   |   |   |   |   | ● |
| asset.edit      | ● |   |   |   |   |   | ● |
| asset.move      | ● |   |   |   |   |   | ● |
| asset.archive   | ● |   |   |   |   |   | ● |
| asset.dispose   | ● |   |   |   |   |   | ● |
| catalog.view    | ● | ● |   |   |   |   | ● |
| catalog.manage  | ● |   |   |   |   |   | ● |
| supplier.view   | ● |   |   |   |   |   | ● |
| supplier.manage | ● |   |   |   |   |   | ● |
| assetuser.view  | ● |   |   |   |   |   | ● |
| assetuser.manage| ● |   |   |   |   |   | ● |
| rate.view       | ● | ● | ● | ● |   | ● | ● |
| rate.set        | ● |   |   |   |   |   | ● |
| report.view     | ● | ● | ● | ● | ● | ● | ● |
| report.export   | ● | ● | ● |   | ● | ● | ● |
| user.manage     |   |   |   |   |   |   | ● |
| role.manage     |   |   |   |   |   |   | ● |

*(Easily adjusted — it's data, not code.)*

---

## 3. Authorization Middleware

Extends the existing `auth.php`:

```php
loadUserAuthorization($conn, $userId);   // at login: cache roles + permissions in session
can('asset.dispose');                    // boolean — for menus, buttons, columns
hasRole('schedule_officer');             // boolean — role check when needed
requirePermission('asset.create');       // page/action guard — 403 if missing
```

Permissions are loaded **once at login** into `$_SESSION` — every page check is an in-memory array lookup, no per-request DB hit. The centralized `partials/sidebar.php` renders each item only when `can(...)` is true.

---

## 4. Phased Migration (always shippable, nothing breaks mid-flight)

| Phase | What happens | Risk | Status |
|-------|-------------|------|--------|
| **A** | Create RBAC tables + seed. Add middleware functions (inert — defined, not called). | None — additive only | ✅ **This delivery** |
| **B** | Wire login to populate session. Add `requirePermission()` to existing pages **in place**. Closes the security hole. | Low — per-page, reversible | Next |
| **C** | Extract shared pages → `modules/assets/…`, `modules/reports/…`. Old `{role}/feature/` paths become thin redirect shims. | Low — shims keep old links alive | After B |
| **D** | Navigation fully permission-driven across all roles. | Low | After C |
| **E** | Quarantine non-asset folders (SIA, accountant/budget accommodation, dir_finance shell) → `/_legacy`. | Low — move, reversible | Last |

### Backward-compatibility guarantees

- `admin_logs` is never modified.
- The 6 shared role-accounts keep working exactly as today.
- `loadUserAuthorization()` is **defensive**: if the RBAC tables don't yet exist, it sets empty roles/permissions and login still succeeds — so the middleware can ship before the SQL is applied without breaking anyone.
- Phase A code is inert until explicitly called in Phase B.

---

## 5. Apply Order

1. Review this strategy. ✅ (done — decisions locked)
2. **Apply `dbs/rbac_schema.sql`** via phpMyAdmin (creates + seeds the 4 tables). ← *next action*
3. Phase B: wire `schedule_officer` + `DSU` login → `loadUserAuthorization()`, then add guards page-by-page.
4. Phases C–E as above.

Nothing in Phase A is wired or enforced. The running system behaves identically until Phase B begins.
