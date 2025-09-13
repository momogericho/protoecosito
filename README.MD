# sitoPol

This application uses environment variables for database configuration. Copy the provided `.env.example` to `.env` and adjust the values for your environment:

```
DB_DSN="mysql:host=localhost;dbname=eco_scambio;charset=utf8mb4"
DB_USER="modificatore"
DB_PASS="Str0ng#Admin9"
```

The configuration is loaded through [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv). Ensure dependencies are installed with Composer:

```
composer install
```

## Application Structure

The 'app/' directory now groups code by responsibility:

-'app/controllers/' -request handlers
-'app/helpers/' -reusable utilities
-'app/initializers/' -setup scripts runby public pages

## Security Guidelines

- **Database access**: use the `Db::prepare()` wrapper for every query. Direct calls to `query()` or string interpolation are forbidden; always bind parameters.
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