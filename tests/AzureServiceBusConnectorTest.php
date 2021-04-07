<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Tests;

use Tests\TestCase;
use Illuminate\Queue\Connectors\ConnectorInterface;
use Illuminate\Contracts\Queue\Queue as QueueInterface;
use OrigoEnergia\AzureServiceBusLaravel\Common\AbstractBaseAzureServiceBus;
use OrigoEnergia\AzureServiceBusLaravel\Context\AzureServiceBus;
use OrigoEnergia\AzureServiceBusLaravel\Connection\AzureServiceBusConnector;
use OrigoEnergia\AzureServiceBusLaravel\Context\ContextInterface;
use OrigoEnergia\AzureServiceBusLaravel\Utils\ConfigurationMock;

final class AzureServiceBusConnectorTest extends TestCase
{
    private ConnectorInterface $azureServiceBusConnectorInstance;

    public function setUp(): void
    {
        parent::setUp();

        $this->azureServiceBusConnectorInstance = new AzureServiceBusConnector();
    }

    public function test_should_return_connected_connector_instance(): void
    {
        $azureServiceBusInstance = $this->azureServiceBusConnectorInstance->connect(ConfigurationMock::getConfigurations());

        $this->assertInstanceOf(ConnectorInterface::class, $this->azureServiceBusConnectorInstance);
        $this->assertInstanceOf(QueueInterface::class, $azureServiceBusInstance);
        $this->assertInstanceOf(AbstractBaseAzureServiceBus::class, $azureServiceBusInstance);
        $this->assertInstanceOf(ContextInterface::class, $azureServiceBusInstance);
        $this->assertInstanceOf(AzureServiceBus::class, $azureServiceBusInstance);
    }
}
