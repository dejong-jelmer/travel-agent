<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Route Groups
    |--------------------------------------------------------------------------
    |
    | The routes that route() knows in the browser and on the SSR server. Public
    | pages get the public group, so the admin routes stay out of their HTML;
    | admin pages get every route (see app.blade.php).
    |
    | @see https://github.com/tighten/ziggy#filtering-routes
    |
    */

    'groups' => [
        'public' => ['!admin.*'],
    ],

];
