# CoworkLibrary - Interactive Co-working & Library Space Management System

A modern, full-stack Laravel application for managing, visualizing, and booking seats in co-working spaces and study libraries. Built with **Laravel 13**, **PHP 8.3**, **Livewire 4**, and **Tailwind CSS v4**.

---

## 📌 Features Overview

- **Public Space Browser & Live Floor Plan**:
  - Interactive floor selection and cinema-style seat row layouts.
  - Live seat status (Available, Occupied, Walkways).
  - Multi-seat reservation modal with real-time price calculation.
- **Customer Registration & Authentication**:
  - OTP verification over email with secure rate limiting.
  - Contact number and optional WhatsApp number collection.
  - Role separation: Students/Customers can book seats; they can upgrade to a space owner anytime from their profile.
- **Owner / Vendor Portal (`/owner/`)**:
  - Interactive floor plan builder with drag-and-drop seat ordering.
  - Custom rows, walkways, seat numbers, bed/desk types, and seat pricing.
  - **Booking Manager**: Review booking requests, approve or reject seats with automated customer email alerts.
  - Profile manager with contact details and WhatsApp toggle.
- **CWL Central Administration Panel (`/cwladminpnl/`)**:
  - Unified **All Users Directory** with real-time filtering (`Customers`, `Vendors`, `Administrators`).
  - One-click account impersonation / switch to any vendor or customer account.
  - Account suspension and reactivation.
  - Platform telemetry: total properties, floors, active bookable seats, and walkway counts.

---

## ⚙️ System Requirements

Ensure your host machine or server has the following installed:

- **PHP**: `^8.3` or higher
  - Required PHP Extensions: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_sqlite` (if using SQLite) or `pdo_mysql` (if using MySQL), `tokenizer`, `xml`
- **Composer**: `^2.x`
- **Node.js**: `^18.x` or `^20.x` (LTS recommended)
- **NPM**: `^9.x` or higher
- **Database**: SQLite (default, zero-configuration) or MySQL `^8.0` / MariaDB `^10.4`

---

## 🚀 Step-by-Step Setup Guide

Follow these steps to set up and run the project on a new system.

### 1. Clone or Extract the Repository

```bash
git clone <repository-url> coworklibrary
cd coworklibrary
```

---

### 2. Install PHP Dependencies

Run Composer to install all backend packages:

```bash
composer install
```

> **Note for Windows Users**: If PHP is installed in a custom location (e.g., `C:\php83`), ensure it is added to your environment `PATH`, or run commands using PowerShell:
> ```powershell
> $env:Path = "C:\php83;" + $env:Path
> ```

---

### 3. Install Node.js Dependencies

Install the frontend packages (Vite, Tailwind CSS, Livewire plugins):

```bash
npm install
```

---

### 4. Setup Environment File (`.env`)

Copy the sample environment file to `.env`:

**Windows (PowerShell / CMD):**
```powershell
copy .env.example .env
```

**Linux / macOS:**
```bash
cp .env.example .env
```

---

### 5. Generate Application Key

```bash
php artisan key:generate
```

---

### 6. Configure the Database

#### Option A: SQLite (Quickest, Zero Config - Recommended for Local Testing)

1. Create an empty SQLite database file in `database/database.sqlite`:

   **Windows (PowerShell):**
   ```powershell
   New-Item -ItemType File -Path database\database.sqlite -Force
   ```

   **Windows (CMD):**
   ```cmd
   type nul > database\database.sqlite
   ```

   **Linux / macOS:**
   ```bash
   touch database/database.sqlite
   ```

2. Verify your `.env` contains:
   ```env
   DB_CONNECTION=sqlite
   ```

#### Option B: MySQL

1. Create a database in your MySQL server (e.g., via phpMyAdmin, HeidiSQL, or MySQL CLI):
   ```sql
   CREATE DATABASE coworklibrary CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Update `.env` with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=coworklibrary
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

---

### 7. Run Migrations & Database Seeders

Run the database migrations along with the default seeders:

```bash
php artisan migrate --seed
```

> 💡 **Tip:** If you ever need to reset the database back to clean demo data:
> ```bash
> php artisan migrate:fresh --seed
> ```

---

### 8. Create Storage Symbolic Link

Enable serving user-uploaded media (property images, avatars, documents):

```bash
php artisan storage:link
```

---

### 9. Build Frontend Assets

Compile CSS and JavaScript bundles using Vite:

```bash
npm run build
```

*(During active frontend development, you can instead run `npm run dev` to enable instant hot-reloading).*

---

### 10. Start the Local Server

Start the Laravel development server:

```bash
php artisan serve
```

The application is now live and accessible at:
👉 **`http://127.0.0.1:8000`**

---

## 🔑 Default Login Credentials & Access URLs

After running `php artisan migrate --seed`, the following accounts are pre-configured:

| Portal | URL | Email | Password | Role / Access |
|---|---|---|---|---|
| **Central Admin Panel** | `http://127.0.0.1:8000/cwladminpnl/login` | `admin@coworklibrary.com` | `admin123` | Full Administrator access to manage users, vendors, properties, and system telemetry |
| **Space Owner / Vendor** | `http://127.0.0.1:8000/owner/` | `admin@example.com` | `password` | Demo space owner with pre-loaded properties, floors, and seat rows |
| **Public / Customer** | `http://127.0.0.1:8000/` | Self-register at `/register` | User chosen | Student/booker account with seat booking capabilities |

---

## 📧 Email & OTP Configuration (How OTP Works)

### Local Development (Default: Log Driver)
By default in `.env`:
```env
MAIL_MAILER=log
```
When a new customer registers at `http://127.0.0.1:8000/register`:
1. The 6-digit verification OTP email is logged to the file:
   📂 `storage/logs/laravel.log`
2. Open that file and search for the latest entry to copy the 6-digit OTP:
   ```text
   Your OTP verification code is: 123456
   ```
3. Enter the OTP into the browser screen to verify the email and log in.

### Production / Live SMTP (e.g., Gmail, Mailtrap, Brevo, Resend)
To send real emails, update `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@coworklibrary.com"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 🛠️ Helpful Artisan & Maintenance Commands

| Task | Command |
|---|---|
| **Clear all caches** | `php artisan optimize:clear` |
| **Run automated test suite** | `php artisan test` |
| **Fix code style (Laravel Pint)** | `vendor/bin/pint` |
| **View route list** | `php artisan route:list` |
| **Run interactive database shell** | `php artisan tinker` |

---

## ❓ Troubleshooting & FAQs

### 1. "Unable to locate file in Vite manifest"
- **Cause**: The frontend bundle hasn't been built or Vite manifest is missing.
- **Fix**: Run `npm run build` or start `npm run dev`.

### 2. "Database file does not exist (SQLite)"
- **Cause**: `database/database.sqlite` file has not been created yet.
- **Fix**: Run `touch database/database.sqlite` (Linux/Mac) or `New-Item -ItemType File -Path database\database.sqlite -Force` (PowerShell).

### 3. "SQLSTATE[HY000]: General error: 5 database is locked" (SQLite)
- **Cause**: Multiple processes writing to the SQLite database simultaneously.
- **Fix**: Restart `php artisan serve` and ensure only one terminal is running migrations or queue workers.

### 4. Admin Panel Login redirecting to `/login`
- **Cause**: Accessing `/cwladminpnl` without an admin session.
- **Fix**: Always log into the central admin portal via `http://127.0.0.1:8000/cwladminpnl/login` using `admin@coworklibrary.com`.

---

## 📄 License

This software is developed and maintained under the [MIT License](LICENSE).
