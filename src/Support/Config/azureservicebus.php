<?php

return [
    'endpoint' => env('AZURE_SERVICE_BUS_ENDPOINT', ''),
    'sharedAccessKeyName' => env('AZURE_SERVICE_BUS_ACCESS_KEY_NAME', 'RootManageSharedAccessKey'),
    'sharedAccessKey' => env('AZURE_SERVICE_BUS_ACCESS_KEY_VALUE', ''),
    // 'secretIssuer' => env('AZURE_SERVICE_BUS_SECRET_ISSUER', ''),
    // 'secretValue' => env('AZURE_SERVICE_BUS_SECRET_ISSUER_VALUE', ''),

    'queues' => [
        'queue_name' => [
            'name' => env('AZURE_SERVICE_BUS_QUEUE_OR_TOPIC_NAME', ''),
        ]
    ],

    'topics' => [
        'topic_name' => [
            'name' => env('AZURE_SERVICE_BUS_QUEUE_OR_TOPIC_NAME', ''),
            'useTopic' => env('AZURE_SERVICE_BUS_ENABLE_TOPIC'),
            'subscription' => env('AZURE_SERVICE_BUS_SUBSCRIPTION_NAME', ''),
        ]
    ],
];
