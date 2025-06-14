# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres
to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

## unstable - 2025-06-14

### Changed

- Changed from codeclimate to qlty.sh
- changed ci.yml
- code formatted correctly
- fixed code smells