# 🧬 Migrations

The Laravel Migration Generator creates migration files from your existing database schema. These migrations are
compatible with Laravel's native migration system and follow a strict ordering to maintain referential integrity.

## 📂 Output Location

Generated migration files are stored in:

```
database/migrations/
```

Each file is timestamped and includes a clear, descriptive name.

## ⚙ How It Works

1. Scans the connected database (MySQL, PostgreSQL, MSSQL)
2. Extracts all tables, columns, indexes, and foreign keys
3. Resolves the correct order of migrations based on dependencies
4. Generates clean, readable Laravel migration files

## 📌 Example

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('content')->nullable();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->timestamps();
});
```

## 🚫 What It Won’t Do

- It won’t drop existing tables or modify your current database
- It doesn’t generate seeders or factories (yet)
- It doesn’t modify `migrations` table entries

## ✅ Migration Ordering

The tool ensures:

- Parent tables are created before child tables (foreign keys)
- Pivot tables come after both referenced tables
- Indexes and constraints are placed at the end of each file
