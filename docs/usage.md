# Usage

After successful installation, you can start generating migration files from an existing database schema.

## Run the Generator

Use the following Artisan command:

```bash
php artisan migrate:regenerate
```

This will:

- Scan your current database
- Create Laravel migration files
- Write them to `database/migrations/`
- Order them to ensure referential integrity

## Supported Databases

- MySQL 5.7 / 8.0
- PostgreSQL 15
- MSSQL 2022

## Laravel Version Support

Compatible with Laravel 5–12.
Older versions are supported via version-specific tags.

## Example Output

The generated migration files will look like this:

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('email')->unique();
    $table->timestamps();
});
```

## Dry-Run Mode (Planned)

Coming soon: preview changes without writing files to disk.

## Notes

- Ensure your `.env` is correctly configured for the DB connection.
- You can customize the export behavior by publishing the config:

```bash
php artisan vendor:publish --tag=migration-generator-config
```