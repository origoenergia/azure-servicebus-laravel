<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Connection;

use OrigoEnergia\AzureServiceBusLaravel\Context\AzureServiceBusContext;

interface ConnectorContextInterface
{
    public static function createContextFor(string $connectionString): AzureServiceBusContext;
}
