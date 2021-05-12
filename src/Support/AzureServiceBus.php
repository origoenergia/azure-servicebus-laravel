<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Support;

use Illuminate\Support\Facades\Facade;

class AzureServiceBus extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'azureservicebus';
    }
}
