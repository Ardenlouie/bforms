<?php

return [
    'name' => 'B-Forms',
    'manifest' => [
        'name' => env('APP_NAME', 'B-Forms'),
        'short_name' => 'B-Forms',
        'start_url' => '/security',
        'background_color' => '#00012b',
        'theme_color' => '#ff0000',
        'display' => 'standalone',
        'orientation'=> 'any',
        'status_bar'=> 'black',
        'icons' => [
            '72x72' => [
                'path' => '/images/icons/logo.png',
                'purpose' => 'any'
            ],
            '96x96' => [
                'path' => '/images/icons/logo.png',
                'purpose' => 'any'
            ],
            '128x128' => [
                'path' => '/images/icons/logo.png',
                'purpose' => 'any'
            ],
            '144x144' => [
                'path' => '/images/icons/logo.png',
                'purpose' => 'any'
            ],
            '152x152' => [
                'path' => '/images/icons/logo.png',
                'purpose' => 'any'
            ],
            '192x192' => [
                'path' => '/images/icons/logo.png',
                'purpose' => 'any'
            ],
            '384x384' => [
                'path' => '/images/icons/logo.png',
                'purpose' => 'any'
            ],
            '512x512' => [
                'path' => '/images/icons/logo.png',
                'purpose' => 'any'
            ],
        ],
        'splash' => [
            '640x1136' => '/images/icons/logo.png',
            '750x1334' => '/images/icons/logo.png',
            '828x1792' => '/images/icons/logo.png',
            '1125x2436' => '/images/icons/logo.png',
            '1242x2208' => '/images/icons/logo.png',
            '1242x2688' => '/images/icons/logo.png',
            '1536x2048' => '/images/icons/logo.png',
            '1668x2224' => '/images/icons/logo.png',
            '1668x2388' => '/images/icons/logo.png',
            '2048x2732' => '/images/icons/logo.png',
        ],
        'shortcuts' => [
            [
                'name' => 'MY FORMS',
                'description' => 'Created B-Forms',
                'url' => '/myforms',
                'icons' => [
                    "src" => "/images/icons/logo.png",
                    "purpose" => "any"
                ]
            ],
            [
                'name' => 'FOR APPROVALS',
                'description' => 'Routed B-Forms for approvals',
                'url' => '/approvers',
                'icons' => [
                    "src" => "/images/icons/logo.png",
                    "purpose" => "any"
                ]
            ]
        ],
        'custom' => []
    ]
];
