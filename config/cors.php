<?php

return [  
    'paths' => ['*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['https://chopoua-madrasha.vercel.app/','http://localhost:7002','https://kebd.mmadinah.com'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
