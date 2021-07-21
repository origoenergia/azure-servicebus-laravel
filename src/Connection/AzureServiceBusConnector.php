<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Connection;

use AzureServiceBus\Common\ServicesBuilder;
use Doctrine\DBAL\Driver\DrizzlePDOMySql\Connection;
use Illuminate\Queue\Connectors\ConnectorInterface;
use Illuminate\Contracts\Queue\Queue as QueueInterface;
use OrigoEnergia\AzureServiceBusLaravel\Context\AzureServiceBusContext;
use OrigoEnergia\AzureServiceBusLaravel\Support\AzureServiceBus;

class AzureServiceBusConnector implements ConnectorInterface, ConnectorContextInterface
{

    /**
     * @param array $configuration
     * @return Illuminate\Contracts\Queue\Queue
     *
     * @example Example for queue configuration.
     *
     * 'azureservicebus' => [
     *    'driver'       => 'azureservicebus',
     *    'endpoint'     => 'https://*.servicebus.windows.net',
     *    'sharedAccessKeyName' => '',
     *    'sharedAccessKey' => '',
     *    'queue'        => 'topic or queue name',
     *    'useTopic' => false,
     *    'subscription' => '',
     *    //'secretIssuer' => '',
     *    //'secretValue' => '',
     *   ];
     */
    public function connect(array $configuration): AzureServiceBusContext
    {
        $enableTopicUse = $configuration['useTopic'] ?? false;
        $destination = $configuration['queue'];
        $subscription = $enableTopicUse ? $configuration['subscription'] : '';

        $connectionString = ConnectionString::createFromArray($configuration);

        $azureServiceInstance = ServicesBuilder::getInstance()->createServiceBusService($connectionString->get());

        return new AzureServiceBusContext($destination, $subscription, $azureServiceInstance);
    }

    public static function createContextFor(string $connectionString): AzureServiceBusContext
    {
        $connectionString = ConnectionString::createFromString($connectionString);
        $azureServiceInstance = ServicesBuilder::getInstance()->createServiceBusService($connectionString->get());

        return new AzureServiceBusContext(null, $connectionString->getKey('entityPath'), $azureServiceInstance);
    }
}
