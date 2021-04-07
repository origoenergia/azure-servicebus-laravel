<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Connection;

class ConnectionString
{
    private static array $params;

    public static function create(array $params): self
    {
        self::$params = $params;

        return new self();
    }

    public function get(): string
    {
        $useSharedAccessKey = !empty(self::$params['sharedAccessKeyName']) && !empty(self::$params['sharedAccessKey']);

        return $useSharedAccessKey ? $this->getWithSharedAccessKeyName() : $this->getWithSharedSecretIssuer();
    }

    public function getWithSharedAccessKeyName(): string
    {
        return 'Endpoint=' . self::$params['endpoint'] . ';SharedAccessKeyName=' . self::$params['sharedAccessKeyName'] . ';SharedAccessKey=' . self::$params['sharedAccessKey'];
    }

    public function getWithSharedSecretIssuer(): string
    {
        return 'Endpoint=' . self::$params['endpoint'] . ';SharedSecretIssuer=' . self::$params['secretIssuer'] . ';SharedSecretValue=' . self::$params['secretValue'];
    }
}
