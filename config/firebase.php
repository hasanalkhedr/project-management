<?php

return [
    'default' => env('FIREBASE_PROJECT', 'app'),

    'projects' => [
        'vitahr-2026' => [
            'credentials' => env(
                'FIREBASE_CREDENTIALS',
                storage_path('app/firebase/vitahr-firebase-service-account.json')
            ),

            'auth' => [
                'tenant_id' => env('FIREBASE_AUTH_TENANT_ID'),
            ],
        ],
    ],
];
