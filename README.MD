# sitoPol

This application uses environment variables for database configuration. Copy the provided `.env.example` to `.env` and adjust the values for your environment:

```
DB_DSN="mysql:host=localhost;dbname=eco_scambio;charset=utf8mb4"
DB_USER_READ="lettore"
DB_PASS_READ="P@ssw0rd!"
DB_USER_WRITE="modificatore"
DB_PASS_WRITE="Str0ng#Admin9"
REMEMBER_KEY="change_me"
```

Set `REMEMBER_KEY` in the environment (e.g., in your `.env` file or deployment configuration) to enable encrypted "ricordami" cookies.

The configuration is loaded through [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv). Ensure dependencies are installed with Composer:

```
composer install
```

## Application Structure

The `app/` directory groups code by responsibility:

- `app/controllers/` – request handlers
- `app/helpers/` – reusable utilities
- `app/initializers/` – setup scripts run by public pages

See [docs/architecture.md](docs/architecture.md) for details on how these
components interact and guidance on adding new ones.

## Security Guidelines

- **Database access**: use `Db::prepareRead()` for SELECTs and `Db::prepareWrite()` for modifications. Direct calls to `query()` or string interpolation are forbidden; always bind parameters.
- **Input validation**: validate and normalise all user provided data before use.
- **Output escaping**: escape dynamic HTML using the `e()` helper (`app/helpers/html_utils.php`).
- **XML parsing**: when parsing XML, use `parseXml()` from `app/helpers/html_utils.php`, which keeps `LIBXML_NOENT` and `LIBXML_DTDLOAD` disabled.

Following these rules helps prevent SQL injection, XSS, and XXE vulnerabilities.

## Request lifecycle

All entry points (files in `public/` and `api/`) bootstrap the application through `app/init.php`:

```php
$csrf = AppInitializer::init();
```

This centralized initializer loads Composer, database configuration, and session management, then prepares a CSRF token for use in forms. Controllers and page initializers assume this step has already run. Any script handling a request should invoke the initializer before executing its own logic.

## Service Worker Cache Versioning

The progressive web app uses a service worker (`public/sw.js`) to cache static and dynamic assets for offline support. Caches are named with a `CACHE_VERSION` identifier. When a new release requires cached assets to be refreshed, increment the value of `CACHE_VERSION`. During the `activate` event the service worker removes caches that do not match the current version, forcing clients to fetch the updated resources.

## HTTPS and Service Worker

Service workers operate only over secure contexts. Deploy the site via `https://` in production or use `http://localhost` during development to allow the offline features to register correctly.
