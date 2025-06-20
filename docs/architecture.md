# 🧱 Project Architecture: Laravel Migration Generator

This document describes the current architecture of the **Laravel Migration Generator** package, including its modular
design and internal flow. It also outlines upcoming structural improvements (e.g. plugin system and model generation).

---

## ⚙️ Core Architecture

The generator is designed around a **modular export pipeline**, consisting of:

1. **Schema Definitions** – Extract raw schema data from supported databases
2. **Internal Representation (DTOs)** – Normalize database structures into a Laravel-agnostic format
3. **Mappers** – Convert normalized definitions into Laravel-compatible PHP migration code
4. **Export Pipeline** – Generate well-ordered migration files with respect to referential integrity

```text
Database -> [Definition Layer] -> [Normalized Internal Structure] -> [Mapper Layer] -> Laravel Migration Files
```

---

## 🧩 Modules

### 1. Definition Layer (`Definition\`)

- Extracts: tables, columns, indexes, foreign keys, and comments
- Converts into internal `TableDefinition`, `ColumnDefinition`, `IndexDefinition`, etc.

### 2. Mapper Layer (`Mapper\`)

- Central abstraction: `AbstractMapper implements MapperInterface`
- Applies Laravel-specific formatting and stub composition logic
- Produces `$table->...` chain strings for each table

### 3. Exporter

- Orders migrations using a dependency resolver (topological sort on foreign keys)
- Writes individual Laravel migration files into `database/migrations/`
- Uses stubs and template placeholders (e.g. `{{className}}`, `{{upMethod}}`, `{{downMethod}}`)

---

## 🛠 Commands

The package registers an Artisan command:

```bash
php artisan migrate:regenerate
```

- Fully automatic generation
- No manual configuration required
- Smart ordering by constraints

---

## 🔌 Plugin System (Planned)

A future plugin architecture will allow third-party or custom modules to:

- Register new formatters/mappers
- Add model generation features
- Inject additional schema inspections or heuristics
- Hook into post-generation steps (e.g. validation, linting)

---

## 🧪 Testing & CI

- GitHub Actions pipeline
- Laravel 10–12 / PHP 8.2–8.4 / MySQL 5.7–8.0 / PostgreSQL 15 / MSSQL 2022
- Full unit test coverage
- Version-aware assertions (to adapt to Laravel internals)

---

## 🔮 Future: Model Generator

Planned feature: `php artisan make:models` or integration into `migrate:regenerate`

- Generates Eloquent models from table definitions
- Supports `fillable`, `casts`, `timestamps`, foreign key relations
- Optional warning for insecure fields (e.g. `password` without hashing)

---

## ✅ Summary

The architecture emphasizes:

- Loose coupling between database logic and Laravel output
- Strong testability and extensibility
- Clear layering for future expansion (e.g. models, plugins, validation)