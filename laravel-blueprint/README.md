# Laravel HMS Blueprint

This folder is not a runnable Laravel app by itself. It is a learning and migration blueprint for the current pure-PHP Hospital Management System.

Use it after creating a real Laravel project:

```powershell
cd D:\Xampp\htdocs
composer create-project laravel/laravel laravel-hms "^12.0"
```

Then copy these blueprint files into the Laravel project and adjust namespaces if needed.

Recommended copy targets:

- `laravel-blueprint/routes/web.php` -> `laravel-hms/routes/web.php`
- `laravel-blueprint/app/Models/*` -> `laravel-hms/app/Models/`
- `laravel-blueprint/app/Http/Middleware/EnsureRole.php` -> `laravel-hms/app/Http/Middleware/EnsureRole.php`
- `laravel-blueprint/database/migrations/*` -> `laravel-hms/database/migrations/`

This blueprint intentionally models a better hospital system than the original database:

- One `users` table for login.
- Role-based access using `role`.
- Separate doctor and patient profiles.
- Appointments assigned to doctors and patients.
- Invoices separated from payments/income.
- Prescriptions and medical records as first-class hospital data.
