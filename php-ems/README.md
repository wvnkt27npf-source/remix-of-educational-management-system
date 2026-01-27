# Educational Management System (Core PHP + CSV)

This folder contains a **cPanel-ready** Core PHP EMS using **CSV files** as storage.

## Quick start (cPanel)
1. Upload the `php-ems/` contents to `public_html/ems/`.
2. Copy `config.sample.php` to `config.php` and adjust values (paths, timezone).
3. Ensure `php-ems/data/` is **writable** by PHP.
4. Visit `/ems/install.php` once to create the first Admin user.
5. Log in at `/ems/login.php`.

## Default install credentials
Created in `install.php`:
- Username: `admin`
- Password: `admin123` (change immediately)

## Data folder protection
`data/.htaccess` blocks direct web access. On some hosts, Apache settings may vary; ensure the data directory is not publicly readable.

## Roles
- **admin**: full access
- **teacher**: manage exams + view students
- **student**: view own profile + class exam schedule + events
- **custom**: full access (implemented via permissions, currently equivalent to admin)
