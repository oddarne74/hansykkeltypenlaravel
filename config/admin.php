<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin User
    |--------------------------------------------------------------------------
    |
    | Credentials used by database/seeders/AdminUserSeeder.php when creating
    | the initial admin user. If no password is configured, a random one is
    | generated and printed to the console.
    |
    */

    'name' => env('ADMIN_NAME', 'Han Sykkeltypen'),
    'email' => env('ADMIN_EMAIL', 'admin@hansykkeltypen.no'),
    'password' => env('ADMIN_PASSWORD'),

];
