# Laravel Migration Generator

[![CI](https://github.com/N3XT0R/laravel-migration-generator/actions/workflows/ci.yml/badge.svg)](https://github.com/N3XT0R/laravel-migration-generator/actions/workflows/ci.yml)
[![Latest Stable Version](https://poser.pugx.org/n3xt0r/laravel-migration-generator/v/stable)](https://packagist.org/packages/n3xt0r/laravel-migration-generator)
[![Code Coverage](https://qlty.sh/badges/dafd3f82-6646-47ae-a73e-3007d27fd67d/test_coverage.svg)](https://qlty.sh/gh/N3XT0R/projects/laravel-migration-generator)
[![Maintainability](https://qlty.sh/badges/dafd3f82-6646-47ae-a73e-3007d27fd67d/maintainability.svg)](https://qlty.sh/gh/N3XT0R/projects/laravel-migration-generator)
[![License](https://poser.pugx.org/n3xt0r/laravel-migration-generator/license)](https://packagist.org/packages/n3xt0r/laravel-migration-generator)
[![Docs](https://img.shields.io/badge/docs-readthedocs-blue)](https://laravel-migration-generator.readthedocs.io/)
[![DB Support](https://img.shields.io/badge/Database%20Support-MySQL%20%2F%20MariaDB%20%7C%20PostgreSQL%20%7C%20MSSQL-blueviolet)](https://laravel-migration-generator.readthedocs.io/)

> 🧱 **Original project launched in May 2020. Recently reactivated and extended with full CI coverage and support for
PostgreSQL, MariaDB, MSSQL and MySQL 8.**
>
> ✅ **CI:** Successfully tested across **MySQL 5.7/8.0**, **PostgreSQL 15**, and **MSSQL 2022** – on **Laravel 10–12**
> and **PHP 8.2–8.4**.

## 📦 Overview

**Laravel Migration Generator**  
_A powerful CLI tool to generate Laravel migration files from an existing MySQL, PostgreSQL or MSSQL database._

This tool provides a structured and extensible approach to reverse‑engineering database schemas into Laravel‑compatible
migration files. It supports foreign key constraints, correct dependency order and customizable mapping logic – enabling
seamless integration into both legacy and modern Laravel projects (Laravel 5–12 supported).

## ✨ Features

- ✅ Detects tables, columns, indexes and foreign keys with precision
- 🔄 Automatically orders migrations to maintain referential integrity
- 🧱 Extensible design via modular definition/mapping architecture
- 🧩 Supports Laravel 5 to 12 (EOL versions maintained in read‑only mode)
- 🛠 Clean, testable and maintainable codebase
- 🧠 Optional schema normalization (via `--normalizer=`), including:
    - Synthetic primary keys for legacy tables with composite keys
    - Automatic preservation of uniqueness constraints
    - Better Eloquent compatibility for complex schemas

## 🔍 Feature Comparison

| Feature / Tool                     | This Package (N3XT0R)               | kitloong                           | bennett‑treptow      | Xethron             |
|------------------------------------|-------------------------------------|------------------------------------|----------------------|---------------------|
| 🛠️ Supported Databases            | ✅ MySQL, PostgreSQL, MSSQL          | ✅ MySQL, PostgreSQL, MSSQL, SQLite | ✅ MySQL only         | ✅ MySQL only        |
| ⚙ Laravel Compatibility            | ✅ 5–12 (version-aware testing)      | ✅ 5–10+                            | ⚠️ up to Laravel 11  | ❌ Legacy only       |
| 🧪 CI/CD with Matrix Testing       | ✅ Full GitHub Actions matrix        | ❌ Partial                          | ❌ Partial            | ❌ None              |
| 🧩 Extensible Mapping Architecture | ✅ Yes (definition + mapping layers) | ❌ Monolithic                       | ❌ No                 | ❌ No                |
| 🔒 Actively Maintained             | ✅ Yes (since 2023)                  | ✅ Yes                              | ✅ Partially          | ❌ No                |
| 🐘 PostgreSQL Support              | ✅ Yes                               | ✅ Yes                              | ❌ No                 | ❌ No                |
| 🪟 MSSQL Support                   | ✅ Yes                               | ✅ Yes                              | ❌ No                 | ❌ No                |
| 💡 Primary Use Case                | ✅ Modern & legacy DB export         | ⚙️ Feature-rich                    | 🧱 MySQL legacy only | 🕰️ Historical tool |

> TL;DR: If you need clean, testable, and modern Laravel migration exports across multiple DB engines – this tool is a
> solid and extensible choice.

## 📊 Version Compatibility

| Laravel/Lumen | PHP Version | Generator Version | Status                 |
|---------------|-------------|-------------------|------------------------|
| 5.x           | 7.2 – 7.4   | 1.0.10            | ❌ EOL                  |
| 6.x           | 7.2 – 7.4   | 1.0.10            | ❌ EOL                  |
| 7.x           | 7.2 – 8.0   | 2.0.0             | ❌ EOL                  |
| 8.x           | 7.3 – 8.0   | 3.0.0             | ❌ EOL                  |
| 9.x           | 8.0         | 4.0.0             | ❌ EOL                  |
| 10.x          | 8.1 – 8.3   | 5.0.0             | ❌ EOL                  |
| 10.x          | 8.1 – 8.3   | 8.3.0             | ❌ EOL                  |
| 11.x          | 8.2 – 8.4   | 6.0.0             | ❌ EOL                  |
| 11.x          | 8.2 – 8.4   | 8.x               | ⚠️ Security fixes only |
| 12.x          | 8.2 – 8.4   | 7.0.0             | ❌ EOL                  |
| 12.x          | 8.2 – 8.4   | 8.x               | ✅ Supported            |

> ⚠️ **Important:** Version 8.0.0 introduces breaking API changes for **Laravel 10–12**.  
> All supported Laravel versions receive the new features and updated APIs, requiring updates to dependent code.

---

## ✅ Database Compatibility

The generator works with all major engines:

### MySQL

| Version | Status      |
|---------|-------------|
| 5.7     | ✅ Supported |
| 8.0     | ✅ Supported |

### PostgreSQL

| Version | Status      |
|---------|-------------|
| 15      | ✅ Supported |

### MSSQL

| Version     | Status      |
|-------------|-------------|
| 2022-latest | ✅ Supported |

> EOL database versions remain functional for legacy compatibility.

## ⚙ Requirements

- PHP ≥ 8.2
    - `pdo_mysql`, `pdo_pgsql` or `pdo_sqlsrv` (depending on your database)
- MySQL ≥ 5.7, PostgreSQL 15 or MSSQL 2022

## 🧰 Installation

Install the package via Composer:

```bash
composer require n3xt0r/laravel-migration-generator --dev
```

Laravel will auto-discover the service provider. No manual registration is needed.

For **Lumen**, register the service provider manually in `bootstrap/app.php`:

```php
$app->register(\N3XT0R\MigrationGenerator\Providers\MigrationGeneratorServiceProvider::class);
```

## 🚀 Usage

Run the migration generator via Artisan:

```bash
php artisan migrate:regenerate
```

This command will generate migration files from your existing database schema into the `database/migrations/` folder.
The files will be ordered automatically to maintain referential integrity – no manual reordering required.

## ⚙️ Custom Export Strategy

If the default export does not meet your needs, the generator is fully extensible. You can override the export logic
through Laravel's Dependency Injection container.

### 🔧 Configuration

First, publish the configuration file:

```bash
php artisan vendor:publish --tag=migration-generator-config
```

Edit `config/migration-generator.php` to adjust or override definitions and mappings.

## 🧩 Export Architecture

The export process is divided into two customizable layers:

### Definition Classes

These classes extract schema information into a **universal, internal representation**. This format is decoupled from
Laravel and can be reused, extended, or mapped differently.

### Mapping Classes

These classes transform the internal representation into **valid Laravel migration code** (PHP). You can override them
to adjust formatting, naming conventions, or structure.

## 🧰 Schema Normalizers

Schema Normalizers are optional pre-processing steps that transform your database schema before migration generation.
They can help adapt legacy structures for better Laravel compatibility.

### Available Normalizers

| Normalizer | Description                                                                                                                                                                                                       |
|------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `pivot`    | Replaces composite primary keys with a synthetic auto-incrementing `id()` column and converts the original composite key into a named `unique()` constraint. Useful for legacy tables incompatible with Eloquent. |

### Usage

Enable a normalizer via CLI:

```bash
php artisan migrate:regenerate --normalizer=pivot
```

Or configure it in `config/migration-generator.php`:

```php
'normalizer' => [
    'enabled' => ['pivot'],
],
```

## 🧪 Testing

To run the tests:

```bash
# mysql 5.7
DB_CONNECTION=mysql DB_HOST=db_migration DB_PASSWORD=dein_db_passwort ./vendor/bin/phpunit

# mysql 8
DB_CONNECTION=mysql DB_HOST=mysql8 DB_PASSWORD=dein_db_passwort ./vendor/bin/phpunit

#postgres
DB_CONNECTION=pgsql DB_HOST=postgres DB_PASSWORD=dein_db_passwort ./vendor/bin/phpunit

# mssql
DB_CONNECTION=sqlsrv DB_HOST=mssql DB_PASSWORD=dein_db_passwort ./vendor/bin/phpunit

```

Docker and CI pipelines are already integrated for continuous validation and quality assurance.

## ✅ CI Test Matrix

This package undergoes continuous integration with GitHub Actions, running tests on an extensive environment matrix
covering Laravel, PHP, and MySQL versions to guarantee robust compatibility and stability.

| Laravel Version | PHP Versions  | MySQL Versions | Number of Jobs |
|-----------------|---------------|----------------|----------------|
| 10              | 8.2, 8.3      | 5.7, 8.0       | 4              |
| 11              | 8.2, 8.3, 8.4 | 5.7, 8.0       | 6              |
| 12              | 8.2, 8.3, 8.4 | 5.7, 8.0       | 6              |

**Total: 16 unique MySQL jobs** plus additional tests for PostgreSQL and MSSQL.

### Key CI features include:

- Dynamic installation of Laravel versions during test runs via Composer.
- Support for PHPUnit 10 and 11, automatically selected per Laravel version.
- Full code coverage reporting with Xdebug and Clover.
- Version-aware assertions adapting test expectations based on Laravel version.

### ℹ️ Composer Compatibility Strategy

Although the root `composer.json` targets Laravel 12 by default, earlier Laravel versions (10, 11) are tested in CI
using dynamic version installation:

```yaml
run: composer require laravel/framework:^${{ matrix.laravel }} --no-interaction --no-update
```

This ensures flexible version handling while keeping the default installation aligned with the latest stable Laravel
release.

### 🧪 Version-Aware Assertions

Table-based tests (e.g., migration sorting or detection) dynamically adjust expected values based on the Laravel
version:

```php
$expectedTables = match (true) {
    str_starts_with(Application::VERSION, '10.') => [...],
    default => [...], // Laravel 11+
};
```

## 📄 License

This project is licensed under the [MIT License](LICENSE).

## 🙌 Contributions

Contributions are welcome! Feel free to open issues or submit pull requests to improve the generator, add new database
support or enhance the customization layers.

## 🔗 Links

- 📦 [Packagist Package](https://packagist.org/packages/n3xt0r/laravel-migration-generator)
- 🧪 [CI & Test Coverage](https://qlty.sh/gh/N3XT0R/projects/laravel-migration-generator)
- 📘 [Laravel Documentation](https://laravel.com/docs)

This README reflects the repository’s current features, including explicit support for MySQL, PostgreSQL and MSSQL,
correct PDO extensions and updated CI information.
