<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Multi-tenancy
    |--------------------------------------------------------------------------
    |
    | Enable multi-tenancy support for the admin panel. When enabled,
    | resources can be scoped to the active tenant.
    |
    */
    'multitenant' => env('PANELIS_MULTITENANT', false),

    /*
    |--------------------------------------------------------------------------
    | Panelis Domain
    |--------------------------------------------------------------------------
    |
    | This value determines the domain used by the Panelis (Filament) admin
    | panel. When set, the admin panel will only be accessible via the
    | specified domain (e.g. panel.domain.com).
    |
    | This allows separation between the public frontend domain and the
    | administration panel domain.
    |
    | Example:
    | APP_PANELIS_DOMAIN=panel.domain.com
    |
    */
    'domain' => env('PANELIS_DOMAIN', ''),

    /*
    |--------------------------------------------------------------------------
    | Panelis Path
    |--------------------------------------------------------------------------
    |
    | This value determines the URI path where the Panelis (Filament)
    | admin panel will be accessible.
    |
    | If a custom domain is not configured, the panel can be accessed
    | via this path under the main application URL.
    |
    | Example:
    | APP_PANELIS_PATH=admin
    | Result:
    | https://domain.com/admin
    |
    | When using a dedicated domain (e.g. panel.domain.com),
    | this value can be set to '/' to make the panel accessible
    | from the root of that domain.
    |
    */
    'path' => env('PANELIS_PATH', ''),

    /*
    |--------------------------------------------------------------------------
    | Application Demo
    |--------------------------------------------------------------------------
    |
    | This value determines the "demo" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */
    'demo' => env('PANELIS_DEMO', false),

    'id' => 'admin',
];
