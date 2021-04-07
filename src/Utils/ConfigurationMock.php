<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Utils;

use Illuminate\Support\Str;

class ConfigurationMock
{
    public static function getConfigurations(): array
    {
        $randomString = strtolower(Str::random(40));
        return [
            'endpoint' => env('AZURE_SERVICE_BUS_ENDPOINT', "https://{$randomString}.servicebus.windows.net"),
            'sharedAccessKeyName' => env('AZURE_SERVICE_BUS_ACCESS_KEY_NAME', 'RootManageSharedAccessKey'),
            'sharedAccessKey' => env('AZURE_SERVICE_BUS_ACCESS_KEY_VALUE', $randomString),
            'queue' => env('AZURE_SERVICE_BUS_QUEUE_OR_TOPIC_NAME', $randomString),
            'useTopic' => env('AZURE_SERVICE_BUS_ENABLE_TOPIC', true),
            'subscription' => env('AZURE_SERVICE_BUS_SUBSCRIPTION_NAME', $randomString),
            'secretIssuer' => env('AZURE_SERVICE_BUS_SECRET_ISSUER', $randomString),
            'secretValue' => env('AZURE_SERVICE_BUS_SECRET_ISSUER_VALUE', $randomString),
        ];
    }
}
