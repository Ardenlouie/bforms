<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'B-FORMS',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => true,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => 'B-FORMS',
    'logo_img' => 'images/logonobg.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'B-FORMS Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => true,
        'img' => [
            'path' => 'images/logonobg.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 300,
            'height' => 300,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => false,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'images/jm-logo-ai.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => true,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => true,
    'usermenu_desc' => true,
    'usermenu_profile_url' => true,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => true,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => false,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'bg-gradient-dark',
    'classes_auth_header' => '',
    'classes_auth_body' => 'bg-gradient-dark',
    'classes_auth_footer' => 'text-center',
    'classes_auth_icon' => 'fa-fw text-light',
    'classes_auth_btn' => 'btn-flat btn-light',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => 'text-md',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'bg-gradient-navy navbar-dark',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Asset Bundling option for the admin panel.
    | Currently, the next modes are supported: 'mix', 'vite' and 'vite_js_only'.
    | When using 'vite_js_only', it's expected that your CSS is imported using
    | JavaScript. Typically, in your application's 'resources/js/app.js' file.
    | If you are not using any of these, leave it as 'false'.
    |
    | For detailed instructions you can look the asset bundling section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        [
            'text'      => 'Home',
            'url'       => 'home',
            'icon'      => 'fas fa-fw fa-home',
            'can'       => 'bforms access',
            'active'    => ['home']
        ],
        [
            'text'      => 'My Forms',
            'url'       => 'myforms',
            'icon'      => 'fas fa-fw fa-file-alt',
            'can'       => 'bforms access',
            'active'    => ['myforms', 'myform*']
        ],
        [
            'text'      => 'For Approvals',
            'url'       => 'approvers',
            'icon'      => 'fas fa-fw fa-file-signature',
            'can'       => 'bforms approver',
            'active'    => ['approvers', 'approver*']
        ],
        
        [
            'text'  => 'HR',
            'url'   => '#',
            'icon'  => 'fa fa-fw fa-users',
            'can'   => ['bforms hr'],
            'submenu' => [
                [
                    'text'      => 'GATE PASS',
                    'url'       => 'forms/list/eyJpdiI6IkpVVUw0QjlmTHNpQ3BTYVorODFKY3c9PSIsInZhbHVlIjoiaU1hby93UDc0UDI1OXo3YktPRkZXUT09IiwibWFjIjoiM2Y5MGEyYjkzZmZlNjQ3MjUyZGVkYmIwYzA2OTNjZjgxZDc4ZTQwYzY1OGJlNzgxNTcwNWUxNDkwMDk0MTI2NSIsInRhZyI6IiJ9',
                    'icon'      => 'fas fa-fw fa-file',
                    'can'       => 'bforms hr'
                ],
            ],
        ],
        [
            'text'  => 'SCM',
            'url'   => '#',
            'icon'  => 'fa fa-fw fa-truck',
            'can'   => ['bforms scm'],
            'submenu' => [
                [
                    'text'      => 'PSRF',
                    'url'       => 'forms/list/eyJpdiI6InVZTW9RQzZCRS9wVVIvZk9lbE9ncUE9PSIsInZhbHVlIjoiYU55UCs3cTJqZ0h3Y1dvK2UrMFMxQT09IiwibWFjIjoiYWNiY2E5MTkwZjNjNzg0MjcyOGZiMmM2MTI0MjNjODA0ZmJiMDQzNDJhMmExMDIwZjBmMDAyODhlZWNkYzY2OSIsInRhZyI6IiJ9',
                    'icon'      => 'fas fa-fw fa-file',
                    'can'       => 'bforms scm'
                ],
                [
                    'text'      => 'PSST',
                    'url'       => 'forms/list/eyJpdiI6ImZrclhIOWtCbGJPOFNqL0V0dmI3b2c9PSIsInZhbHVlIjoiMGVLN0FtWE9lSnVCUU55WkRBVzlBQT09IiwibWFjIjoiOTgzODQ2NjVhZDNjM2IyNWU0ODIzODJiNmRhODVmOWEzMjk3ZTNlMTUzMTk4MzdiZDJiYjIzYzU4YTU4ZDliOSIsInRhZyI6IiJ9',
                    'icon'      => 'fas fa-fw fa-file',
                    'can'       => 'bforms scm'
                ],
            ],
        ],
        [
            'text'  => 'Finance',
            'url'   => '#',
            'icon'  => 'fa fa-fw fa-dollar-sign',
            'can'   => ['bforms finance'],
            'submenu' => [
                [
                    'text'      => 'Request for Payment',
                    'url'       => 'forms/list/eyJpdiI6IlE4UDNhamtUclBvNzNPaGJZMUd1V3c9PSIsInZhbHVlIjoiNngvdVVTbS9yNTZPalVSRGZNVVE0UT09IiwibWFjIjoiMTFiZGIxM2Q1ZDIzMDE5OGNmODA5NDhmMTkyNDYzN2RhZTZkM2Y1MjcyNmE4ZmM3ZjgxM2VhN2Q3NWI4ZGIwYSIsInRhZyI6IiJ9',
                    'icon'      => 'fas fa-fw fa-file',
                    'can'       => 'bforms finance',
                ],
                [
                    'text'      => 'Cash Advance',
                    'url'       => 'forms/list/eyJpdiI6IkhReWU4UUN5b1REb2NQS2F4S211MVE9PSIsInZhbHVlIjoiaW84MEFnY3JtYXFVbHVKa2NaQjJLUT09IiwibWFjIjoiZDk2MzNmMzJkZThjNTcxNDcyNGU4NzdhZmM1M2UzNDYwMGFmN2JkMTUxOTI5YzYxNjUwMTYwMzVmOGNlNjhmYyIsInRhZyI6IiJ9',
                    'icon'      => 'fas fa-fw fa-file',
                    'can'       => 'bforms finance',
                ],
                [
                    'text'      => 'Cash Advance (Liquidation)',
                    'url'       => 'forms/list/eyJpdiI6Im45SThiaHl2cGNiSy9uVmY3a2RzOUE9PSIsInZhbHVlIjoiaEdvL0NnZ3lrRGJtaEN1TUZFR2Q3UT09IiwibWFjIjoiZGU0MmY0N2JlMzRmNGE5NTVhMjU1YWIxM2U4ZDQ3ODFkMDQwY2Q5ZmIwNDA2MjBlNzcyYmEwZDUzZTQxOTlmZiIsInRhZyI6IiJ9',
                    'icon'      => 'fas fa-fw fa-file',
                    'can'       => 'bforms finance',
                ],
                [
                    'text'      => 'Petty Cash Advance',
                    'url'       => 'forms/list/eyJpdiI6ImNpWTNFTHMrWlZUUWdCQ3M1Uzc2bGc9PSIsInZhbHVlIjoiSFB1M1puK1luUmQwZEJQVVFVYVNtdz09IiwibWFjIjoiZDYyZTQwOGUyYzdmYjc2N2Y3OGYxNmUyYWRjMzJjNzgxNDNhYjYzYTJlM2YzYjY1YThhYzQ2M2I5ZGM1Nzc5ZSIsInRhZyI6IiJ9',
                    'icon'      => 'fas fa-fw fa-file',
                    'can'       => 'bforms finance',
                ],
                [
                    'text'      => 'Petty Cash Advance (Liquidation)',
                    'url'       => 'forms/list/eyJpdiI6ImdKK21waFVQeitjdTh6U3prVnoxYXc9PSIsInZhbHVlIjoiWlBVdDh1Z3NNRy9BbXBMUzRXcjdSUT09IiwibWFjIjoiMjQ2NzE3NzdkMmYzMTZhMGYxZGE2ZTQ0OGQ3MDMyNTVjZmJjMmRkOWRkODNiNDFhYzhlNTdiZDg5YmI0ZDAwYSIsInRhZyI6IiJ9',
                    'icon'      => 'fas fa-fw fa-file',
                    'can'       => 'bforms finance',
                ],
                [
                    'text'      => 'PSRF',
                    'url'       => 'forms/list/eyJpdiI6InVZTW9RQzZCRS9wVVIvZk9lbE9ncUE9PSIsInZhbHVlIjoiYU55UCs3cTJqZ0h3Y1dvK2UrMFMxQT09IiwibWFjIjoiYWNiY2E5MTkwZjNjNzg0MjcyOGZiMmM2MTI0MjNjODA0ZmJiMDQzNDJhMmExMDIwZjBmMDAyODhlZWNkYzY2OSIsInRhZyI6IiJ9',
                    'icon'      => 'fas fa-fw fa-file',
                    'can'       => 'bforms finance',
                ],
            ],
        ],
        [
            'text'      => 'All Forms',
            'url'       => 'allforms',
            'icon'      => 'fas fa-fw fa-folder-open',
            'can'       => 'superadmin access',
            'active'    => ['allforms', 'allform*']
        ],
        [
            'text'  => 'settings',
            'url'   => '#',
            'icon'  => 'fa fa-fw fa-cog',
            'can'   => ['superadmin access'],
            'submenu' => [
                // [
                //     'text'      => 'org_structures',
                //     'url'       => 'org-structures',
                //     'icon'      => 'fas fa-fw fa-code-branch',
                //     'can'       => 'org structure access',
                //     'active'    => ['org-structures', 'org-structure*']
                // ],
                [
                    'text'      => 'Forms',
                    'url'       => 'forms',
                    'icon'      => 'fas fa-fw fa-file',
                    'can'       => 'superadmin access',
                    'active'    => ['forms']
                ],
                [
                    'text'      => 'companies',
                    'url'       => 'companies',
                    'icon'      => 'fas fa-fw fa-building',
                    'can'       => 'superadmin access',
                    'active'    => ['companies', 'company*']
                ],
                [
                    'text'      => 'Departments',
                    'url'       => 'departments',
                    'icon'      => 'fas fa-fw fa-layer-group',
                    'can'       => 'superadmin access',
                    'active'    => ['departments', 'department*']
                ],
                [
                    'text'      => 'positions',
                    'url'       => 'positions',
                    'icon'      => 'fas fa-fw fa-user-tag',
                    'can'       => 'superadmin access',
                    'active'    => ['positions', 'position*']
                ],
                [
                    'text'      => 'users',
                    'url'       => 'users',
                    'icon'      => 'fas fa-fw fa-users',
                    'can'       => 'superadmin access',
                    'active'    => ['users', 'user*']
                ],
                [
                    'text'      => 'roles',
                    'url'       => 'roles',
                    'icon'      => 'fas fa-fw fa-user-lock',
                    'can'       => 'superadmin access',
                    'active'    => ['roles', 'role*']
                ],
                [
                    'text'      => 'system_settings',
                    'url'       => 'system-setting',
                    'icon'      => 'fas fa-fw fa-cogs',
                    'can'       => 'superadmin access',
                    'active'    => ['system-setting']
                ],
                [
                    'text'      => 'system_logs',
                    'url'       => 'system-logs',
                    'icon'      => 'fas fa-fw fa-stream',
                    'can'       => 'superadmin access',
                    'active'    => ['system-logs']
                ],
                [
                    'text'      => 'error_logs',
                    'url'       => 'error-logs',
                    'icon'      => 'fas fa-fw fa-bug',
                    'can'       => 'superadmin access',
                    'active'    => ['error-logs']
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@11',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
        'iCheckBoostrap' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => './vendor/icheck-bootstrap/icheck-bootstrap.min.css',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => true,

    'gmail_login' => true,
];
