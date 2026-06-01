# Laravel Migration Plan

Laravel is the best long-term direction if you want to learn modern PHP and make this Hospital Management System easier to maintain.

This machine currently has PHP 8.2 through XAMPP, but Composer is not installed on PATH. Laravel normally starts with Composer, so install Composer first.

Official Laravel installation docs: https://laravel.com/docs/12.x/installation

## Why Laravel Helps

Laravel gives the project:

- Routes instead of scattered page files.
- Controllers instead of form logic inside HTML pages.
- Models and Eloquent relationships instead of raw SQL everywhere.
- Migrations instead of manual SQL dumps.
- Middleware for auth and role checks.
- Blade templates for layouts.
- Form request validation.
- File upload storage helpers.
- Password hashing built in.
- CSRF protection built in.

## Recommended Version

Use Laravel 12 for this project because the local XAMPP PHP version is 8.2.12. Newer Laravel versions may require newer PHP.

## Create The Laravel App

After Composer is installed:

```powershell
cd D:\Xampp\htdocs
composer create-project laravel/laravel laravel-hms "^12.0"
cd laravel-hms
php artisan serve
```

Then configure `.env`:

```env
APP_NAME="Hospital Management System"
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hms_laravel
DB_USERNAME=root
DB_PASSWORD=root
```

## Laravel Structure For This HMS

```text
app/
  Http/
    Controllers/
      Admin/
      Doctor/
      Patient/
      AppointmentController.php
      BillingController.php
      ReportController.php
    Middleware/
      EnsureRole.php
    Requests/
  Models/
    User.php
    DoctorProfile.php
    PatientProfile.php
    Appointment.php
    Prescription.php
    Invoice.php
    Payment.php
    Department.php
database/
  migrations/
resources/
  views/
    layouts/
    admin/
    doctor/
    patient/
routes/
  web.php
```

## Model Mapping

Current table | Laravel model | Better table name
---|---|---
`admin` | `User` | `users`
`doctors` | `DoctorProfile` | `doctor_profiles`
`patient` | `PatientProfile` | `patient_profiles`
`appointment` | `Appointment` | `appointments`
`income` | `Invoice` / `Payment` | `invoices`, `payments`
`report` | `Report` | `reports`

## Laravel Learning Order

1. Routing: `routes/web.php`
2. Controllers: `php artisan make:controller`
3. Blade layouts: `resources/views/layouts/app.blade.php`
4. Migrations: `php artisan make:migration`
5. Eloquent models and relationships.
6. Authentication and middleware.
7. Validation with Form Request classes.
8. File uploads with Laravel Storage.

## Migration Strategy

Do not convert everything at once.

1. Build Laravel authentication with `users.role`.
2. Build dashboards for admin, doctor, patient.
3. Port patient registration.
4. Port doctor application and admin approval.
5. Port appointment booking.
6. Port discharge/invoice flow.
7. Add prescriptions and medical records.

The `laravel-blueprint/` folder in this repo contains starter files that show how the Laravel version should be shaped.
