<?php

return [
    'install'    =>    [

        // Enable / Disable `neodash:install` command
        'command'    =>    env('APP_DEBUG', true),

        /*
        | Modules
        |---------------------
        | Modules are those segments which comes with `akhilesh/neodash`.
        | All modules specified here will be installed after executing `neodash:install` command
        |-------------------------------------
        | Available Modules: default, faqs, pages, testimonials
        */
        'modules'    =>    [
            'default',
            #'faqs',
            #'pages',
            #'testimonials'
        ],

        /*
        | Packages
        |---------------------
        | These are third party packages other than `akhilesh/neodash`.
        | There are several packages which can be used for improvements of project.
        |-------------------------------------
        | Some Packages: takshak/adash-blog, takshak/adash-slider, barryvdh/laravel-debugbar
        */
        'packages'    =>    [
            #'takshak/adash-blog',
            #'takshak/adash-slider',
            #'barryvdh/laravel-debugbar --dev'
        ],
    ],

    'site'    =>    [
        'name'          =>    'Neodash',
        'short_name'    =>    'NEOD',
        'logo_full'     =>    '',
        'logo_short'    =>    '',
        'favicon'       =>    '',
    ],

    'primary_mail' => env('MAIL_PRIMARY', 'hello@example.com'),

    /*
    | Imager
    |---------------------
    | For configuration of package: `takshak/imager`.
    */
    'imager'    =>    [
        'picsum'    =>    [
            'enable_url'    =>    true,
        ],
        'placeholder'    =>    [
            'enable_url'    =>    true,
        ],
    ],
];
