<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Tests;

use Tests\TestCase;
use Illuminate\Support\Str;
use OrigoEnergia\AzureServiceBusLaravel\Connection\ConnectionString;
use OrigoEnergia\AzureServiceBusLaravel\Utils\ConfigurationMock;

final class ConnectionStringTest extends TestCase
{
    private array $configurations;

    public function setUp(): void
    {
        $this->configurations = ConfigurationMock::getConfigurations();
    }

    public function test_should_return_azure_service_bus_connection_string_for_shared_access_key(): void
    {
        $endpointRegex = '/Endpoint=https:\/\/([a-zA-Z0-9\-\_\.]*\.servicebus\.windows\.net)\;SharedAccessKeyName=([A-Za-z]){1,}\;SharedAccessKey=([A-Za-z0-9_~\/\+\=\-!@#\$%\^&\*\(\)]){1,}/';

        $connectionStringInstance = ConnectionString::create($this->configurations);

        $connectionStringWithSharedAcessKey = $connectionStringInstance->getWithSharedAccessKeyName();

        $this->assertInstanceOf(ConnectionString::class, $connectionStringInstance);
        $this->assertNotNull($connectionStringWithSharedAcessKey);
        $this->assertMatchesRegularExpression($endpointRegex, $connectionStringWithSharedAcessKey);
    }

    public function test_should_return_azure_service_bus_connection_string_for_shared_secret_issuer(): void
    {
        unset($this->configurations['sharedAccessKeyName']);
        unset($this->configurations['sharedAccessKey']);

        $randomString = strtolower(Str::random(40));

        $this->configurations['secretissuer'] = $randomString;
        $this->configurations['secret'] = $randomString;

        $endpointRegex = '/Endpoint=https:\/\/([a-zA-Z0-9\-\_\.]*\.servicebus\.windows\.net)\;SharedSecretIssuer=([A-Za-z0-9_~\/\+\=\-!@#\$%\^&\*\(\)]){1,}\;SharedSecretValue=([A-Za-z0-9_~\/\+\=\-!@#\$%\^&\*\(\)]){1,}/';
        $connectionStringInstance = ConnectionString::create($this->configurations);

        $connectionStringSharedSecretIssuer = $connectionStringInstance->getWithSharedSecretIssuer();

        $this->assertInstanceOf(ConnectionString::class, $connectionStringInstance);
        $this->assertNotNull($connectionStringSharedSecretIssuer);
        $this->assertMatchesRegularExpression($endpointRegex, $connectionStringSharedSecretIssuer);
    }
}
