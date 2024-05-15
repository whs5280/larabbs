<?php

return [
    // 高德地图
    'gaodei' => [
        'app_key' => env('GAODEI_APP_KEY', ''),
    ],
    // 腾讯IM
    'im' => [
        'app_key' => env('IM_APP_KEY', ''),
    ],
    // 腾讯推送
    'tpns' => [
        'access_id'  => env('PUSH_ACCESS_ID', ''),
        'access_key' => env('PUSH_ACCESS_SECRET', ''),
        'secret_key' => env('PUSH_SECRET_KEY', ''),
        'account_prefix' => env('PUSH_ACCOUNT_PREFIX', ''),
    ],
];
