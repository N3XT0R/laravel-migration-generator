# 📦 Installation Guide

This guide explains how to install the **Laravel Migration Generator** into your Laravel or Lumen application.

---

## ✅ Requirements

- **PHP** ≥ 8.2
- Laravel 5.x – 12.x supported
- One of the following PHP extensions, depending on your database:
    - `pdo_mysql` (MySQL / MariaDB)
    - `pdo_pgsql` (PostgreSQL)
    - `pdo_sqlsrv` (SQL Server)

Supported databases:

- ✅ MySQL 5.7, 8.0
- ✅ PostgreSQL 15
- ✅ Microsoft SQL Server 2022+

---

## 🚀 Installation via Composer

Install the package in your Laravel project:

```bash
composer require n3xt0r/laravel-migration-generator --dev
```

> ℹ️ This package is intended as a **development dependency**.

Laravel will automatically discover the service provider.

---

## 🛠 Lumen Support

For Lumen, you must register the service provider manually:

1. Open `bootstrap/app.php`
2. Add the following line:

```php
$app->register(\N3XT0R\MigrationGenerator\Providers\MigrationGeneratorServiceProvider::class);
```

---

## ⚙️ Publish Configuration (optional)

If you want to customize behavior (e.g. table filters, naming), you can publish the configuration file:

```bash
php artisan vendor:publish --tag=migration-generator-config
```

This will create the file:

```
config/migration-generator.php
```

Inside, you can override table filters, data type mappings, and export behavior.

---

## 🐳 Docker Setup (Optional)

This project includes a ready-to-use `docker-compose.yml` for local testing with multiple databases.

### 🔧 Usage

Simply run:

```bash
docker-compose up -d
```

This will start local containers for:

- `mysql8` (MySQL 8.0)
- `postgres` (PostgreSQL 15)
- `mssql` (SQL Server 2022)

You can now run Artisan commands against each engine using environment overrides:

```bash
DB_CONNECTION=mysql php artisan migrate:regenerate
DB_CONNECTION=pgsql php artisan migrate:regenerate
DB_CONNECTION=sqlsrv php artisan migrate:regenerate
```

> Ensure your `.env` is properly configured or use inline overrides.

---

## ✅ Next Step

➡️ [Continue to the Usage Guide ›](usage.md)
