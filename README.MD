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