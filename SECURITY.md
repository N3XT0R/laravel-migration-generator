# Security Policy

## 📬 Reporting a Vulnerability

If you discover a security vulnerability within this package, **please do not disclose it publicly**. Instead, report it
directly and confidentially via:

- Email: `info@php-dev.info`
- GitHub Security Advisory: [Submit here](https://github.com/N3XT0R/laravel-migration-generator/security/advisories/new)

We aim to respond to all valid reports within **72 hours** and will coordinate disclosure responsibly.

## 🔒 Supported Versions

| Version | Status        | Notes                     |
|---------|---------------|---------------------------|
| 9.x     | ✅ Supported   | Actively maintained       |
| < 8.0   | ❌ Unsupported | Legacy, no security fixes |

If you're using an unsupported version, we strongly recommend upgrading to the latest stable release.

## 🧪 Security Considerations

This package performs introspection on database schemas and writes files to disk. As such, you should:

- Avoid using it with untrusted or manipulated database schemas.
- Run generators only in trusted development environments.
- Always verify generated migrations and models before committing.

## 🔐 Dependencies

Dependencies are managed via Composer and adhere
to [FriendsOfPHP/security-advisories](https://github.com/FriendsOfPHP/security-advisories) via `composer audit`.

Use:

```bash
composer audit
