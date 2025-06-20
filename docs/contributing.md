# Contributing

Thank you for considering contributing to the Laravel Migration Generator!  
Whether it's reporting a bug, suggesting a feature, or submitting a pull request — you're welcome.

### Getting Started

1. **Fork the repository**
2. **Clone it locally**
3. **Install dependencies via Composer**
4. **Run the test suite** to ensure everything is working:

```bash
composer install
./vendor/bin/phpunit
```

### Recommended Environment (via Docker)

You can use the included `docker-compose.yml` to spin up MySQL, PostgreSQL, and MSSQL instances for testing:

```bash
docker-compose up -d
```

Set the correct `DB_CONNECTION` and credentials in your terminal before running PHPUnit.

---

### Contribution Ideas

- Improve database coverage (e.g. field types, foreign key edge cases)
- Extend test scenarios and assertion coverage
- Add new database backends (e.g. SQLite or Oracle)
- Improve documentation

---

Please follow PSR-12 coding standards. Code style will be validated automatically in CI.
