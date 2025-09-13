# Application Architecture

The `app/` directory groups the application's server-side PHP code. Each
subdirectory has a clear responsibility:

## `app/controllers/`

Controllers handle incoming requests, orchestrate models, and determine the
appropriate response. They expose classes or functions that can be called from
initializer scripts.

### Interaction and loading
Initializers include controllers using `require_once`. Controllers may in turn
load models or helpers to complete their tasks.

### Adding a controller
1. Create `app/controllers/your_controller.php`.
2. Define a class (e.g. `YourController`) or functions to encapsulate the
   behaviour.
3. `require_once` any models or helpers the controller depends on.
4. Include and instantiate the controller from an initializer to use it in a
   page.

## `app/helpers/`

Helpers provide reusable utility functions such as validation, HTML escaping, or
session management.

### Interaction and loading
Helpers are included with `require_once` by controllers or initializers as
needed. `bootstrap.php` loads the session helper automatically to start a secure
session.

### Adding a helper
1. Create `app/helpers/your_helper.php` and declare your functions.
2. Keep helpers stateless and focused on a single purpose.
3. Include the helper where required using `require_once`.

## `app/initializers/`

Initializers are scripts executed before rendering a public page. They load the
application environment, prepare controllers, and handle form submissions.

### Interaction and loading
Each file in `public/` begins by requiring `bootstrap.php` and then includes an
initializer from this directory. The initializer sets up dependencies,
processes input, and exposes variables for templates. It delegates business
logic to controllers and uses helpers as needed.

### Adding an initializer
1. Create `app/initializers/your_initializer.php`.
2. Start with `require_once __DIR__ . '/../../bootstrap.php';`.
3. `require_once` any controllers or helpers it needs.
4. Handle request data and make variables available to templates.
5. Include the initializer from a `public/*.php` entry point.


## `resources/views/`
View templates live under `resources/views/` and are split by purpose:

- `pages/` contains full-page templates such as `home_view.php` or
  `lista_view.php`.
- `partials/` holds reusable fragments like `header.php`, `footer.php`,
  `menu.php`, and `user_status.php`.
  
Templates are included via the `BASE_VIEW_PATH` constant defined in
`bootstrap.php` to avoid fragile relative paths:

```php
require BASE_VIEW_PATH.'/partials/header.php';
require BASE_VIEW_PATH.'/pages/home_view.php';
```

## Loading flow overview
1. A `public/*.php` entry point requires `bootstrap.php`.
2. `bootstrap.php` loads Composer autoloading, database configuration, and the
   session helper which starts a secure session.
3. The entry point includes an initializer.
4. The initializer loads controllers and helpers, performs request processing,
   and prepares data.
5. Templates render the final HTML for the user.

Maintaining this structure keeps responsibilities clear and makes adding new
features predictable.