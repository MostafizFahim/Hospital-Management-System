# Hospital Management System

A PHP and MySQL based web application for managing basic hospital activities through separate panels for admin, doctors, and patients.

## Project Overview

This project is a simple Hospital Management System built with PHP, MySQL, Bootstrap, and HTML/CSS. It provides user-facing pages for patients and doctors, plus an admin login area for managing hospital-related information. The project uses a MySQL database named `hmsDB` and includes an SQL dump file, `hmsdb.sql`, for creating the required database tables.

## Features

- Home page with hospital management navigation
- Admin login system
- Doctor login and doctor account workflow
- Patient account creation and patient login workflow
- Doctor application option
- MySQL database integration
- Bootstrap based responsive layout
- Image-based UI sections for doctor, patient, and information panels

## Technologies Used

| Technology | Purpose |
|---|---|
| PHP | Backend and server-side logic |
| MySQL / MariaDB | Database |
| HTML | Page structure |
| CSS / Bootstrap | Styling and responsive layout |
| JavaScript / jQuery | Frontend interaction support |
| XAMPP | Recommended local development environment |

## Project Structure

```text
Hospital-Management-System/
├── admin/                  # Admin dashboard files
├── doctor/                 # Doctor panel files
├── patient/                # Patient panel files
├── include/                # Shared files such as header and database connection
│   ├── connection.php
│   └── header.php
├── img/                    # Project images and UI assets
├── hmsdb.sql               # Database SQL dump
├── index.php               # Main home page
├── adminLogin.php          # Admin login page
├── doctorlogin.php         # Doctor login page
├── patientlogin.php        # Patient login page
├── account.php             # Patient account creation page
├── apply.php               # Doctor application page
└── README.md
```

## Requirements

Before running the project, install:

- XAMPP, WAMP, Laragon, or any PHP local server
- PHP 7.4 or newer
- MySQL or MariaDB
- Web browser

Recommended setup: **XAMPP** because it includes Apache, PHP, MySQL/MariaDB, and phpMyAdmin in one package.

## Database Configuration

The database connection is configured in:

```text
include/connection.php
```

Current default connection:

```php
$connect = mysqli_connect("localhost", "root", "", "hmsDB");
```

So the project expects:

| Item | Value |
|---|---|
| Host | `localhost` |
| Username | `root` |
| Password | empty password |
| Database | `hmsDB` |
| SQL file | `hmsdb.sql` |

## How to Run with XAMPP and phpMyAdmin

1. Download or clone the repository:

```bash
git clone https://github.com/MostafizFahim/Hospital-Management-System.git
```

2. Copy the project folder into the XAMPP `htdocs` directory:

```text
C:\xampp\htdocs\Hospital-Management-System
```

3. Start **Apache** and **MySQL** from the XAMPP Control Panel.

4. Open phpMyAdmin:

```text
http://localhost/phpmyadmin
```

5. Create a new database named:

```text
hmsDB
```

6. Select the `hmsDB` database.

7. Go to the **Import** tab.

8. Choose the `hmsdb.sql` file from the project folder.

9. Click **Go** to import the database.

10. Open the project in your browser:

```text
http://localhost/Hospital-Management-System/
```

## How to Run without XAMPP/phpMyAdmin

You can also run the project using PHP's built-in server and MySQL CLI.

1. Clone the repository:

```bash
git clone https://github.com/MostafizFahim/Hospital-Management-System.git
cd Hospital-Management-System
```

2. Create the database from MySQL CLI:

```bash
mysql -u root -p -e "CREATE DATABASE hmsDB;"
```

3. Import the SQL file:

```bash
mysql -u root -p hmsDB < hmsdb.sql
```

4. Start PHP local server from the project root:

```bash
php -S localhost:8000
```

5. Open the project:

```text
http://localhost:8000
```

If your MySQL username or password is different, update `include/connection.php`.

## Default Login Information

The SQL dump includes sample users.

### Admin Login

| Username | Password |
|---|---|
| `Mostafiz` | `12345` |
| `fahim` | `123` |

Admin login page:

```text
http://localhost/Hospital-Management-System/adminLogin.php
```

For PHP built-in server:

```text
http://localhost:8000/adminLogin.php
```

### Sample Doctor

| Username | Password | Status |
|---|---|---|
| `jaman` | `$ new` | Approved |

### Sample Patient

| Username | Password |
|---|---|
| `jaman` | `12345` |

## Important URLs

| Page | URL |
|---|---|
| Home | `/index.php` |
| Admin Login | `/adminLogin.php` |
| Doctor Login | `/doctorlogin.php` |
| Patient Login | `/patientlogin.php` |
| Patient Account | `/account.php` |
| Doctor Application | `/apply.php` |

## Common Problems and Fixes

### Database connection failed

Check `include/connection.php` and confirm that the database name, username, and password match your local MySQL setup.

### `hmsDB` not found

Create the database first, then import `hmsdb.sql`.

### Page shows PHP code instead of running

Open the project through Apache or PHP server. Do not open `.php` files directly by double-clicking them.

Correct:

```text
http://localhost/Hospital-Management-System/
```

Wrong:

```text
file:///C:/xampp/htdocs/Hospital-Management-System/index.php
```

### Images not loading

Make sure the `img/` folder remains inside the project and file names are unchanged.

### Login not working

Import `hmsdb.sql` again and use the sample credentials from this README.

## Suggested Future Updates

- Use prepared statements to prevent SQL injection
- Hash user passwords instead of storing plain text passwords
- Add form validation on both frontend and backend
- Improve folder structure using MVC pattern
- Add appointment management
- Add prescription and report management
- Add dashboard statistics
- Improve UI design and mobile responsiveness
- Add role-based access control

## Author

**Mostafiz Fahim**  
GitHub: [MostafizFahim](https://github.com/MostafizFahim)

## License

This project is for academic and learning purposes. You can modify and improve it for your own practice.
