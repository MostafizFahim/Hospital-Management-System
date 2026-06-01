# Hospital Management System Refactor Plan

This project is currently a procedural PHP application. It works page by page, but the code mixes routing, database queries, validation, session checks, business logic, and HTML in the same files.

The goal of the next refactor is to keep the project easy to understand while making it organized enough to maintain.

## Current State

- Pure PHP, HTML, Bootstrap, jQuery, and MySQL.
- No framework.
- No routing layer.
- No model or repository layer.
- Database queries are repeated directly inside page files.
- Admin, doctor, and patient profile logic is mostly duplicated.
- Authentication is improved but still procedural.
- Layout is partially centralized through `include/header.php` and role sidebars.

## Recommended Pure PHP Structure

```text
Hospital-Management-System/
  public/
    index.php
    assets/
  app/
    Controllers/
      AdminController.php
      AuthController.php
      DoctorController.php
      PatientController.php
      AppointmentController.php
      BillingController.php
      ReportController.php
    Repositories/
      AdminRepository.php
      DoctorRepository.php
      PatientRepository.php
      AppointmentRepository.php
      InvoiceRepository.php
      ReportRepository.php
    Services/
      AuthService.php
      ProfileService.php
      AppointmentService.php
      BillingService.php
      UploadService.php
    Views/
      layouts/
      admin/
      doctor/
      patient/
  config/
    database.php
  database/
    schema.sql
  core/
    auth.php
    database.php
    helpers.php
    validation.php
```

## Refactor Order

1. Move reusable helpers into `core/`.
2. Move database connection into `config/database.php`.
3. Replace repeated direct SQL with repository functions.
4. Extract repeated profile update/password update logic into `ProfileService`.
5. Extract appointment booking/discharge/invoice logic into services.
6. Replace duplicated HTML with layouts and view partials.
7. Add a small router only after the page logic is clean.

## Repeated Logic To Remove First

- Login query and password verification.
- Logout/session destruction.
- Profile image upload.
- Username change.
- Password change.
- Patient table rendering.
- Doctor table rendering.
- Appointment/invoice table rendering.
- Alert/error display.

## HMS Logic Gaps

The current app has the beginning of a hospital system, but not a complete one.

Missing or weak areas:

- Doctor availability and schedules.
- Appointment assignment to a specific doctor.
- Appointment approval, cancellation, and rescheduling.
- Prescriptions.
- Diagnosis notes.
- Medical history.
- Lab tests and lab reports.
- Patient admission and discharge records.
- Rooms and beds.
- Departments.
- Payment status tracking.
- Invoice table separated from income/payments.
- Staff roles beyond admin/doctor/patient.
- Role-based permissions.
- Activity logs/audit trail.
- Search, filtering, and pagination.
- Better upload validation.

## Good Next Feature Order

1. Doctor schedules.
2. Appointment doctor assignment.
3. Appointment status flow: pending, approved, completed, cancelled.
4. Prescriptions.
5. Medical records.
6. Billing and payment status.
7. Admin reporting dashboards.
