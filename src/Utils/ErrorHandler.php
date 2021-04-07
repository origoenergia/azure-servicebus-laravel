<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Utils;

use Exception;
use AzureServiceBus\ServiceBus\Internal\IServiceBus;

class ErrorHandler
{
    private const STATUS_CODE_CONFLICT = 409;

    public static function handle(Exception $e, ?IServiceBus $azureServiceBusClient = null): IServiceBus
    {
        switch ($e->getCode()) {
            case self::STATUS_CODE_CONFLICT:
                return $azureServiceBusClient;
            default:
                throw $e;
                break;
        }
    }
}
