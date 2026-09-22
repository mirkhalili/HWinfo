# Docker session/CSRF deployment notes

The PHP runtime must persist sessions for the lifetime of a browser session. The application now explicitly uses `IHW_SESSION_PATH` when provided, otherwise `/tmp/hwinfo-sessions`, and configures HttpOnly/SameSite=Lax cookies.

Recommended PHP service environment:

```yaml
environment:
  IHW_DB_DSN: "mysql:host=db;port=3306;dbname=ihw;charset=utf8mb4"
  IHW_DB_USER: "ihw"
  IHW_DB_PASS: "ihw@1405"
  IHW_SESSION_PATH: "/var/lib/php/sessions"
```

Recommended PHP volume:

```yaml
volumes:
  - ./ihw:/var/www/html
  - php_sessions:/var/lib/php/sessions
```

Add the named volume:

```yaml
volumes:
  db_data:
  php_sessions:
```

If using the recommended persistent session path, create it in the PHP image:

```dockerfile
RUN mkdir -p /var/lib/php/sessions \
    && chown -R www-data:www-data /var/lib/php/sessions \
    && chmod 700 /var/lib/php/sessions
```

After changing the Dockerfile:

```bash
docker compose down
docker compose up -d --build
```

Verify:

```bash
docker exec -it php-fpm php -i | grep -E 'session.save_path|session.cookie'
docker exec -it php-fpm php -r 'session_start(); echo session_save_path(), PHP_EOL;'
```

The login form must be loaded again after deployment so the browser receives a fresh session cookie and CSRF token. If an old session cookie remains, clear the site's cookies or use a private window for the first test.

Do not disable CSRF validation.
