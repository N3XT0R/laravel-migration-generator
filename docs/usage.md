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

## Schema Normalization

### Motivation

Legacy database schemas often use **composite primary keys**, which are **not supported** by Laravel's Eloquent ORM.
This can cause issues when working with relationships, factories, and model binding.

To address this, the migration generator offers an optional **normalization step** that transforms composite primary
keys into a **synthetic, single-column primary key**.

### How It Works

When the `--normalizer=primary-key` option is enabled:

1. **Composite primary keys** are detected and removed.
2. A synthetic `$table->id()` column is added as the new primary key.
3. The original composite key is retained as a **named unique constraint**:
   ```php
   $table->unique(['user_id', 'role_id'], 'role_user_user_id_role_id_unique');
   ```

### Example

**Legacy schema:**

```sql
CREATE TABLE role_user
(
    user_id INT,
    role_id INT,
    PRIMARY KEY (user_id, role_id)
);
```

**Normalized Laravel migration:**

```php
Schema::create('role_user', function (Blueprint $table) {
    $table->id(); // synthetic primary key
    $table->unsignedBigInteger('user_id');
    $table->unsignedBigInteger('role_id');

    $table->unique(['user_id', 'role_id'], 'role_user_user_id_role_id_unique');
    // Original primary key: PRIMARY KEY (user_id, role_id)
});
```

### How to Enable

Use the CLI option during migration generation:

```bash
php artisan migrate:regenerate --normalizer=primary-key
```

You can also enable it via the config file:

```php
// config/migration-generator.php
'normalizer' => [
    'enabled' => ['primary-key'],
],
```

### Notes

- This feature is **non-destructive**: the original uniqueness semantics are preserved.
- Designed for compatibility with Laravel’s Eloquent, seeding, and testing tools.
- Runs during the **pre-generation transformation phase**, before compilation.

## Dry-Run Mode (Planned)

Coming soon: preview changes without writing files to disk.

## Notes

- Ensure your `.env` is correctly configured for the DB connection.
- You can customize the export behavior by publishing the config:

```bash
php artisan vendor:publish --tag=migration-generator-config
```