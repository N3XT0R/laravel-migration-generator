# Laravel Migration Generator

[![CI](https://github.com/N3XT0R/laravel-migration-generator/actions/workflows/ci.yml/badge.svg)](https://github.com/N3XT0R/laravel-migration-generator/actions/workflows/ci.yml)
[![Latest Stable Version](https://poser.pugx.org/n3xt0r/laravel-migration-generator/v/stable)](https://packagist.org/packages/n3xt0r/laravel-migration-generator)
[![Code Coverage](https://qlty.sh/badges/dafd3f82-6646-47ae-a73e-3007d27fd67d/test_coverage.svg)](https://qlty.sh/gh/N3XT0R/projects/laravel-migration-generator)
[![Maintainability](https://qlty.sh/badges/dafd3f82-6646-47ae-a73e-3007d27fd67d/maintainability.svg)](https://qlty.sh/gh/N3XT0R/projects/laravel-migration-generator)
[![License](https://poser.pugx.org/n3xt0r/laravel-migration-generator/license)](https://packagist.org/packages/n3xt0r/laravel-migration-generator)

## 📦 Overview

**Laravel Migration Generator**  
_A powerful CLI tool to generate Laravel migration files from an existing MySQL database schema._

This tool provides a structured and extensible approach to reverse-engineering database schemas into Laravel-compatible
migration files. It supports foreign key constraints, correct dependency order, and customizable mapping logic –
enabling seamless integration into both legacy and modern Laravel projects (Laravel 5–12 supported).

## ✨ Features

- ✅ Detects tables, columns, indexes, and foreign keys with precision
- 🔄 Automatically orders migrations to maintain referential integrity
- 🧱 Extensible design via modular definition/mapping architecture
- 🧩 Supports Laravel 5 to 12 (EOL versions maintained in read-only mode)
- 🛠 Clean, testable, and maintainable codebase

## 📊 Version Compatibility

| Laravel/Lumen | PHP Version | Generator Version | Status      |
|---------------|-------------|-------------------|-------------|
| 5.x           | 7.2 – 7.4   | 1.0.10            | ❌ EOL       |
| 6.x           | 7.2 – 7.4   | 1.0.10            | ❌ EOL       |
| 7.x           | 7.2 – 8.0   | 2.0.0             | ❌ EOL       |
| 8.x           | 7.3 – 8.0   | 3.0.0             | ❌ EOL       |
| 9.x           | 8.0         | 4.0.0             | ❌ EOL       |
| 10.x          | 8.1 – 8.3   | 5.0.0             | ✅ Supported |
| 11.x          | 8.2 – 8.4   | 6.0.0             | ✅ Supported |
| 12.x          | 8.2 – 8.4   | 7.0.0             | ✅ Supported |

> ⚠️ EOL versions are not actively developed but remain functional for legacy compatibility.

## ⚙ Requirements

- PHP ≥ 8.2
    - `pdo_mysql` extension
- MySQL ≥ 5.7

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

This command will generate migration files from your existing MySQL schema into the `database/migrations/` folder. The
files will be ordered automatically to maintain referential integrity – no manual reordering required.

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

## 🧪 Testing

To run the tests:

```bash
./vendor/bin/phpunit
```

Docker and CI pipelines are already integrated for continuous validation and quality assurance.

## ✅ CI Test Matrix

This package is continuously tested across multiple Laravel and PHP versions to ensure full compatibility and
reliability.

| Laravel Version | PHP Versions  | Database  |
|-----------------|---------------|-----------|
| 10              | 8.2, 8.3      | MySQL 5.7 |
| 11              | 8.2, 8.3, 8.4 | MySQL 5.7 |
| 12              | 8.2, 8.3, 8.4 | MySQL 5.7 |

CI is powered by GitHub Actions and includes:

- Multiple Laravel versions via dynamic `composer require`
- PHPUnit 10/11 support (auto-detected per version)
- Full code coverage with Clover reports (Xdebug enabled)
- Version-aware tests (e.g., different table sets for Laravel 10 vs. 11+)

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
support (e.g., PostgreSQL), or enhance the customization layers.

## 🔗 Links

- 📦 [Packagist Package](https://packagist.org/packages/n3xt0r/laravel-migration-generator)
- 🧪 [CI & Test Coverage](https://qlty.sh/gh/N3XT0R/projects/laravel-migration-generator)
- 📘 [Laravel Documentation](https://laravel.com/docs)
