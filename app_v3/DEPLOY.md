# Asset Register v3 — Deployment Checklist

## Prerequisites
- PHP 8.1+, Apache with mod_rewrite, MySQL 8.0+
- `mbstring`, `mysqli` PHP extensions enabled

## 1. Database Setup

### Fresh install
```sql
-- Run in order:
SOURCE database/migrations/001_initial_schema.sql
SOURCE database/seeds/asset_classes.sql
```

### Existing v1/v2 installation
```sql
-- Run the incremental migration:
SOURCE database/migrations/003_v3_schema_changes.sql
```

> **Note on 003**: Steps are idempotent (IF EXISTS / IF NOT EXISTS guards).
> Still, **take a full database backup first**.

## 2. Environment Configuration

```bash
cp .env.example .env
```

Edit `.env`:
```
APP_NAME=Asset Register
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com/asset_register/app_v3/public

DB_HOST=localhost
DB_NAME=asset_register_new
DB_USER=your_db_user
DB_PASS=your_strong_password

SESSION_NAME=ar_v3
SESSION_LIFETIME=1800
APP_SECRET=<run: php -r "echo bin2hex(random_bytes(32));">
```

## 3. Web Server

Point the virtual host document root to `app_v3/public/`.

```apache
<VirtualHost *:443>
    DocumentRoot /var/www/html/asset_register/app_v3/public
    <Directory ...>
        AllowOverride All
        Options -Indexes
    </Directory>
</VirtualHost>
```

Or, if running in a subdirectory, set `APP_URL` to the full URL path including
the subdirectory (the router strips the base path automatically).

## 4. File Permissions

```bash
chmod 750 app_v3/src app_v3/database
chmod 700 app_v3/.env
chmod -R 770 app_v3/storage/
chown -R www-data:www-data app_v3/storage/
```

`.env` must **not** be readable by the web server directly; the `.htaccess`
`<FilesMatch>` rule blocks it at the Apache level.

## 5. Default Users

The seed file creates 7 users, all with password `password` (bcrypt cost 12).
**Change all passwords immediately after first login** via Admin → Users.

| Username          | Role              |
|-------------------|-------------------|
| hod_ict           | hod_ict           |
| schedule_officer  | schedule_officer  |
| budget_officer    | budget_officer    |
| accountant        | accountant        |
| director_finance  | director_finance  |
| sia               | sia               |
| dsu               | dsu               |

## 6. Post-Deploy Security Checks

- [ ] `APP_DEBUG=false` in production `.env`
- [ ] `.env` not web-accessible (test: `curl https://yourdomain.com/.env` → 403)
- [ ] HTTPS configured; uncomment HSTS header in `.htaccess` after verified
- [ ] All default passwords changed
- [ ] Active dollar rate set in Admin → Dollar Rate
- [ ] `storage/` directory not web-accessible
- [ ] Error logs reviewed: `storage/logs/`

## 7. Cutover from v2

1. Put v2 into read-only mode (disable write actions)
2. Run `003_v3_schema_changes.sql` on the live database
3. Deploy v3 to the web root
4. Run smoke tests:
   - [ ] Login works for each role
   - [ ] Dashboard loads with correct asset count
   - [ ] Asset list shows all records
   - [ ] Summary report matches v2 figures for the same year
   - [ ] CSV export downloads correctly
5. Announce cutover to users
