# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres
to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

[Unreleased]

## [8.4.1] - 2026-01-07

### Fixed

- Fixed an issue where MySQL ENUM columns caused migration generation to fail due to missing Doctrine DBAL type mapping.

## [8.4.0] – 2025-12-23

### Added

- Introduced support for PHP 8.5

## [8.3.0] – 2025-10-26

### Changed

- Maintenance release with dependency updates and stability improvements

## [8.2.0] – 2025-07-06

### Added

- Introduced SchemaMigrationExecutor and corresponding interface to separate execution logic from console command (
  MigrationGeneratorCommand).
- Added support for Laravel's container instantiation of SchemaMigrationExecutorInterface via service provider.
- Enabled optional injection of SchemaNormalizationManagerInterface into executor for improved configurability.

### Changed

- Refactored MigrationGeneratorCommand to delegate migration execution to the new SchemaMigrationExecutor, aligning with
  the Separation of Concerns (SoC) principle.
- Normalizer resolution is now conditional and injected only if active normalizers are defined.

## [8.1.0] – 2025-07-04

### Added

- Support for schema normalization via `--normalizer=pivot`.
  This feature replaces composite primary keys with a synthetic `$table->id()` column,
  preserving the original key as a named `UNIQUE` constraint.
  Useful for Eloquent compatibility with legacy schemas.

## [8.0.1]

### Chore

- pushed tag with RTD-Docs

## [8.0.0]

### Breaking Changes

- Introduced major API changes affecting Laravel 10, 11, and 12.
- Legacy compatibility paths removed — all supported versions now share the same updated core functionality.
- Requires users to update dependent code to comply with new interfaces and behaviors.

### Added

- Unified support for MySQL, PostgreSQL, and MS SQL Server with improved database abstraction.
- Enhanced migration generation logic with improved sorting and constraint handling.
- New factory pattern implementation for schema parsers per database driver.
- Expanded CI test matrix covering Laravel 10–12, PHP 8.2–8.4, and multiple database versions (MySQL, PostgreSQL,
  MSSQL).

### Fixed

- Corrected table sorting logic for complex foreign key constraints.
- Updated PHPUnit compatibility to support versions 10 and 11.

### Notes

- Users upgrading from versions <8.0.0 must review code for interface changes, especially related to schema parser
  usage.
- Version 8.0.0 replaces all prior versions for Laravel 10–12; backward compatibility with old APIs is not maintained.

## [7.2.0] - 2025-06-15

### Added

- Full support for **MySQL 8.0+**, including foreign key resolution via `referential_constraints` (fallback for legacy
  `INNODB_SYS_FOREIGN` removed).
- CI matrix extended to cover **Laravel 10–12**, **PHP 8.2–8.4**, and **MySQL 5.7 & 8.0** combinations (16 total jobs).
- Version-aware foreign key parser logic with automatic fallback depending on MySQL version.
- Tests for table detection and sorting now handle Laravel 10 vs 11+ table differences automatically.

### Changed

- Refactored `getRefNameByConstraintName()` to detect MySQL version at runtime and apply appropriate query strategy.
- Internal test assertions adjusted to reflect changes in Laravel default table sets (e.g. `personal_access_tokens`).

### Fixed

- Tests failing under MySQL 8 due to removed `INNODB_SYS_FOREIGN` view.

### Maintenance

- CI infrastructure cleaned up and matrix stabilized for upcoming PHP 8.4 final.

## [7.1.0] - 2025-06-15

### Added

- Added qlty cli tool to docker container

### Changed

- Changed from codeclimate to qlty.sh
- changed ci.yml
- code formatted correctly
- fixed code smells/removed codeclimate.yml

## [7.0.0] - 2025-06-14

### Changed

- upgraded to Laravel 12.x
- changed app.dockerfile/docker-compose.yml and added xdebug with xdebug.mode=coverage
- changed xdebug.ini with memory_limit = -1 for unittests when xdebug.ini is loaded in docker-compose.yml

## [6.0.0] - 2025-06-14

### Added

- added test environments for PHP 8.2, 8.3 and 8.4

### Changed

- upgraded to Laravel 11.x
- migrated all tests to PHPUnit 11.5

## [5.0.0] - 2025-06-14

### Changed

- upgraded to Laravel 10.x
- changed readme
- changed tests equivalent to support DBAL 3
- migrated to Laravel 10
- changed DBAL Version from 2.x to 3.x

## [4.0.2] - 2025-06-14

### Changed

- changed readme

## [4.0.1] - 2025-06-14

### Changed

- changed readme

## [4.0.0] - 2025-06-14

### Added

- Typed Properties added to every Class

### Changed

- migrated to Laravel 9

### Removed

- Dropped Support for PHP 7.x

## [3.0.0] - 2025-06-14

### Changed

- migrated to Laravel 8 / PHPUnit 9.5
- Changed Travis-CI to Github Actions

## [2.0.0] - 2021-01-16

### Added

- Added support for laravel/lumen up from 7.

### Changed

- Changed compatibility to php 7.2 or 8 and higher.

### Removed

- Dropped support for laravel/lumen 5/6 [EOL]

## [1.0.10] - 2020-06-24

### Fixed

- Bug inside of foreign keys fixed.
