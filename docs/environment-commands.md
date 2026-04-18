# Environment and Database Commands

This project now uses a single `.env` file.

The active runtime values stay in the normal keys such as:

- `APP_ENV`
- `APP_DEBUG`
- `APP_URL`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

The stored profiles live in the same `.env` file using prefixes:

- `PRODUCTION_*`
- `SANDBOX_*`

Example:

```dotenv
APP_ENV=sandbox
DB_HOST=127.0.0.1
DB_DATABASE=gymspot_sandbox

PRODUCTION_APP_ENV=production
PRODUCTION_APP_DEBUG=false
PRODUCTION_APP_URL=https://gymspot.pt
PRODUCTION_DB_CONNECTION=mysql
PRODUCTION_DB_HOST=94.46.182.53
PRODUCTION_DB_PORT=3306
PRODUCTION_DB_DATABASE=pedromir_gymspot
PRODUCTION_DB_USERNAME=pedromir_gymspot
PRODUCTION_DB_PASSWORD="secret"

SANDBOX_APP_ENV=sandbox
SANDBOX_APP_DEBUG=true
SANDBOX_APP_URL=https://gymspot.pt
SANDBOX_DB_CONNECTION=mysql
SANDBOX_DB_HOST=127.0.0.1
SANDBOX_DB_PORT=3306
SANDBOX_DB_DATABASE=gymspot_sandbox
SANDBOX_DB_USERNAME=root
SANDBOX_DB_PASSWORD=
```

## Command: switch active environment

```bash
php artisan app:env-switch sandbox
php artisan app:env-switch production
```

What it does:

- Validates that the requested profile exists inside `.env`.
- Creates a timestamped backup of the current `.env`.
- Rewrites the active `APP_*` and `DB_*` keys from the matching profile.
- Runs `php artisan optimize:clear`.

Optional flag:

```bash
php artisan app:env-switch sandbox --no-backup
```

## Command: clone production DB into sandbox

```bash
php artisan db:clone-production-to-sandbox
```

What it does:

- Reads MySQL credentials from `PRODUCTION_DB_*` inside `.env`.
- Reads MySQL credentials from `SANDBOX_DB_*` inside `.env`.
- Creates a SQL dump from production.
- Drops and recreates the sandbox database.
- Imports the production dump into sandbox.

Optional flags:

```bash
php artisan db:clone-production-to-sandbox --keep-dump
php artisan db:clone-production-to-sandbox --mysqldump="C:\\xampp\\mysql\\bin\\mysqldump.exe" --mysql="C:\\xampp\\mysql\\bin\\mysql.exe"
```

## Important notes

- The clone command supports `DB_CONNECTION=mysql` only.
- The sandbox database is fully overwritten.
- The command aborts if production and sandbox resolve to the same host, port and database name.
- The MySQL user configured for sandbox must have permission to drop and create the sandbox database.
- On Windows, you may need to pass explicit paths to `mysqldump.exe` and `mysql.exe`.

## Suggested workflow

1. Keep both `PRODUCTION_*` and `SANDBOX_*` values in `.env`.
2. Run `php artisan db:clone-production-to-sandbox`.
3. Run `php artisan app:env-switch sandbox`.
4. When needed, return with `php artisan app:env-switch production`.
