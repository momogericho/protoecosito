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

## Security Guidelines

- **Database access**: use the `Db::prepare()` wrapper for every query. Direct calls to `query()` or string interpolation are forbidden; always bind parameters.
- **Input validation**: validate and normalise all user provided data before use.
- **Output escaping**: escape dynamic HTML using the `e()` helper (`app/html_utils.php`).
- **XML parsing**: when parsing XML, use `parseXml()` from `app/html_utils.php`, which keeps `LIBXML_NOENT` and `LIBXML_DTDLOAD` disabled.

Following these rules helps prevent SQL injection, XSS, and XXE vulnerabilities.