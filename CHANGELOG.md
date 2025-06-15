# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres
to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [master] - 2022-02-17

## Changed

- migrating away from travis for CI.

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

## [3.0.0] - 2025-06-14

### Changed

- migrated to Laravel 8 / PHPUnit 9.5
- Changed Travis-CI to Github Actions

## [4.0.0] - 2025-06-14

### Added

- Typed Properties added to every Class

### Changed

- migrated to Laravel 9

### Removed

- Dropped Support for PHP 7.x

## [4.0.1] - 2025-06-14

### Changed

- changed readme

## [4.0.2] - 2025-06-14

### Changed

- changed readme

## [5.0.0] - 2025-06-14

### Changed

- upgraded to Laravel 10.x
- changed readme
- changed tests equivalent to support DBAL 3
- migrated to Laravel 10
- changed DBAL Version from 2.x to 3.x

## [6.0.0] - 2025-06-14

### Added

- added test environments for PHP 8.2, 8.3 and 8.4

### Changed

- upgraded to Laravel 11.x
- migrated all tests to PHPUnit 11.5

## [7.0.0] - 2025-06-14

### Changed

- upgraded to Laravel 12.x
- changed app.dockerfile/docker-compose.yml and added xdebug with xdebug.mode=coverage
- changed xdebug.ini with memory_limit = -1 for unittests when xdebug.ini is loaded in docker-compose.yml

## [7.1.0] - 2025-06-15

### Added

- Added qlty cli tool to docker container

### Changed

- Changed from codeclimate to qlty.sh
- changed ci.yml
- code formatted correctly
- fixed code smells/removed codeclimate.yml

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
