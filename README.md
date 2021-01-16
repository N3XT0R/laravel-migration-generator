# laravel-migration-generator [5.x / 6.x]
[![Build Status](https://travis-ci.com/N3XT0R/laravel-migration-generator.svg?branch=master)](https://travis-ci.com/N3XT0R/laravel-migration-generator)
[![Latest Stable Version](https://poser.pugx.org/n3xt0r/laravel-migration-generator/v/stable)](https://packagist.org/packages/n3xt0r/laravel-migration-generator)
[![Test Coverage](https://api.codeclimate.com/v1/badges/3be6f76e8df15784a025/test_coverage)](https://codeclimate.com/github/N3XT0R/laravel-migration-generator/test_coverage)
[![Maintainability](https://api.codeclimate.com/v1/badges/3be6f76e8df15784a025/maintainability)](https://codeclimate.com/github/N3XT0R/laravel-migration-generator/maintainability)
[![License](https://poser.pugx.org/n3xt0r/laravel-migration-generator/license)](https://packagist.org/packages/n3xt0r/laravel-migration-generator)

This Migration-Generator generates Migrations in correct order, when referential integrity is used.
So on remigrating them at example on a dev-system, local-system or others its possible
to migrate the schema without constraint violations.

## Requirements

- PHP 7.2 or higher (php 8 is also supported)
    - Pdo_mysql extension
- MySQL 5.7 or higher

### Additional Info

This Library was tested with following Laravel/Lumen-Versions:

- 7

## Installation

You can install this package over composer via 

``
composer require n3xt0r/laravel-migration-generator
``

You`ll not need to add any ServiceProviders to your Configuration on Laravel,
this package will register itself on your project.

When you are using Lumen, make sure you have added following line to your app.php:

``
$app->register(\N3XT0R\MigrationGenerator\Providers\MigrationGeneratorServiceProvider::class);
``

### Executing the Migrator from Artisan

This Migrator can be executed over the command line by using following command:

``
php artisan migrate:regenerate
``

It will dump all your tables to the database/migrations folder in correct order.
So when you are using referential integrity it will write all migrations so that they could be
re-migrated without changing manually the order of the migration-files.


### Custom Export

Are you unhappy with the exported migrations? When you should need some customizations on it,
it would be possible to customize the export by extending the export-classes over the DI-Container.

There is a Configuration file called "migration-generator", that you could publish and customize.
Every Export-Function like "exporting fields" or "exporting indexes" or else has a Definition- and a Mapping-Class.

#### Definition-Classes 

Definition-Classes are classes that define the internal runtime export-format. 
They are used to generate the Schema-Results to a universal format, so that you or anyone else could extend it.

#### Mapping-Classes

Mapping Classes are classes that converts internal universal format to executable php-code inside
the migration-classes.
