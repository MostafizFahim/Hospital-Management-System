# Hospital Management System

A raw PHP, HTML, Bootstrap, and MySQL Hospital Management System for learning full-stack PHP without a framework. The project has separate admin, doctor, and patient panels and now includes a more realistic appointment, discharge, invoice, and prescription workflow.

## Current Version

This is still a **plain PHP project**, not Laravel and not any other PHP framework. It uses reusable PHP includes, shared helpers, role-based session checks, MySQLi prepared statements in key flows, and a shared Bootstrap-based UI.

Laravel migration notes are included for later learning, but the runnable app is the root PHP/MySQL project.

## Features

- Public home page
- Admin login and dashboard
- Doctor login and dashboard
- Patient registration/login/dashboard
- Doctor application and admin approval/rejection
- Patient appointment booking with approved doctor selection
- Admin appointment approval/cancellation before doctor visit
- Doctor appointment queue for approved assigned appointments
- Doctor discharge workflow with validation
- Invoice generation with unpaid/paid/waived payment status
- Admin billing desk for collecting or waiving invoices
- Prescription/diagnosis record creation during discharge
- Patient invoice and prescription view
- Patient report submission
- Admin reports, income, doctors, patients, job requests, and appointments overview
- Protected role-based pages
- Secure logout with full session destruction
- Password hashing with backward-compatible legacy password upgrade
- Basic input validation and output escaping
- Safer image uploads for profile photos
- Modernized home, login, dashboard, navigation, sidebar, card, form, and table styling
- Future Laravel migration blueprint in `laravel-blueprint/`

## Technologies

| Technology | Purpose |
|---|---|
| PHP 8+ | Backend logic |
| MySQL / MariaDB | Database |
| HTML | Page structure |
| Bootstrap 5 | UI styling |
| jQuery | Existing AJAX support |
| XAMPP | Recommended local development stack |

## Project Structure

```text
Hospital-Management-System/
  admin/                 Admin panel pages
  doctor/                Doctor panel pages
  patient/               Patient panel pages
  include/
    connection.php       DB config, helpers, validation helpers
    auth.php             Auth guard and logout helper
    header.php           Shared styles, scripts, top navigation
  docs/                  Architecture and Laravel migration notes
  laravel-blueprint/     Future Laravel reference files only
  img/                   Public images
  hmsdb.sql              Database schema and sample data
  index.php              Home page
```

## Requirements

- XAMPP, WAMP, Laragon, or another PHP local server
- PHP 8.0 or newer recommended
- MySQL or MariaDB
- Browser

## Database Configuration

The database settings are in:

```text
include/connection.php
```

Current local configuration:

```php
$host = "localhost";
$user = "root";
$password = "root";
$database = "hmsDB";
```

If your MySQL root password is empty, change `$password` to `""`.

## Setup With XAMPP

1. Put the project in:

```text
D:\Xampp\htdocs\Hospital-Management-System
```

2. Start Apache and MySQL from XAMPP.

3. Open phpMyAdmin:

```text
http://localhost/phpmyadmin
```

4. Create a database:

```text
hmsDB
```

5. Import:

```text
hmsdb.sql
```

6. Open:

```text
http://localhost/Hospital-Management-System/
```

## Run With PHP Built-In Server

If PHP is available:

```powershell
cd D:\Xampp\htdocs\Hospital-Management-System
D:\Xampp\php\php.exe -S 127.0.0.1:8090
```

Then open:

```text
http://127.0.0.1:8090/
```

## Default Login Credentials

The SQL dump uses hashed passwords, but these plain credentials still work.

### Admin

| Username | Password |
|---|---|
| `Mostafiz` | `12345` |
| `fahim` | `123` |

### Doctor

| Username | Password | Status |
|---|---|---|
| `jaman` | `$ new` | Approved |

### Patient

| Username | Password |
|---|---|
| `jaman` | `12345` |

## Main URLs

| Page | URL |
|---|---|
| Home | `/index.php` |
| Admin Login | `/adminLogin.php` |
| Doctor Login | `/doctorlogin.php` |
| Patient Login | `/patientlogin.php` |
| Patient Registration | `/account.php` |
| Doctor Application | `/apply.php` |
| Admin Dashboard | `/admin/index.php` |
| Doctor Dashboard | `/doctor/index.php` |
| Patient Dashboard | `/patient/index.php` |

## Real-Life HMS Workflow

1. Patient creates an account or logs in.
2. Patient books an appointment and selects an approved doctor.
3. Admin reviews the request and approves or cancels the appointment.
4. Doctor sees only assigned approved appointments.
5. Doctor checks appointment details.
6. Doctor discharges the patient with:
   - invoice fee
   - billing description
   - diagnosis
   - medicine/prescription
   - advice
   - optional follow-up date
7. The system creates an unpaid invoice.
8. Admin marks the invoice as paid, unpaid, or waived from the billing desk.
9. Patient views invoice payment status and prescription details.
10. Admin tracks appointments, billing, reports, doctors, patients, and job requests.

## Security Improvements

- Session ID regeneration after login.
- Full session/cookie cleanup on logout.
- Role-based access guard for admin, doctor, and patient pages.
- Password hashing with `password_hash`.
- Legacy plain-text password support only for upgrade.
- Output escaping through helper function.
- Prepared query helpers for safer database operations.
- Input validation for core forms.
- Profile image upload validation by size/type/extension.

## Composer And Laravel

Composer is PHP's dependency manager, similar to npm for JavaScript. Laravel needs Composer to install Laravel and its packages.

Composer is not required to run this raw PHP project.

For future Laravel learning, see:

- `docs/LARAVEL_MIGRATION_PLAN.md`
- `laravel-blueprint/`

## Known Limitations

This is still a learning project. It is more realistic now, but a production HMS would still need:

- CSRF tokens on all forms
- More complete prepared-statement coverage
- Dedicated departments, rooms, beds, staff, lab tests, and admissions
- Appointment time slots and doctor availability calendars
- Pharmacy, lab, and inventory modules
- Pagination/search/filtering for large data
- Better audit logs
- Email/SMS notifications
- Stronger authorization policies
- More robust file storage

## Author

Mostafiz Fahim

## License

For academic and learning purposes.
