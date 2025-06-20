# Configuration

After installation, you may optionally publish the configuration file to customize export behavior:

```bash
php artisan vendor:publish --tag=migration-generator-config
```

This will create a file at:

```
config/migration-generator.php
```

### Key Configuration Options

- **`definitions`**  
  Maps the database type (e.g. `mysql`, `pgsql`, `sqlsrv`) to the corresponding schema extractor class.

- **`mappings`**  
  Maps internal field types and structures to Laravel migration statements. Can be overridden for custom formatting or
  behavior.

You can freely modify or extend these mappings to suit your project structure or Laravel conventions.
