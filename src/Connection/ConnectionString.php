<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Connection;

class ConnectionString
{
    private static array $params;
    private static string $string;

    public static function createFromArray(array $params): self
    {
        self::$params = $params;

        return new self();
    }

    public static function createFromString(string $string): self
    {
        self::$string = $string;

        return (new self())->extract();
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

    private function extract(): self
    {
        self::$string = $this->fromSbToHttpsProtocol(self::$string);

        $connectionStringData = str_replace(['Endpoint=', 'SharedAccessKeyName=', 'SharedAccessKey=', 'EntityPath='], [''], self::$string);
        $connectionStringData = explode(';', $connectionStringData);
        $connectionStringData = [
            'endpoint' => $connectionStringData[0],
            'sharedAccessKeyName' => $connectionStringData[1],
            'sharedAccessKey' => $connectionStringData[2],
            'entityPath' => $connectionStringData[3],
        ];

        self::$params = $connectionStringData;

        return $this;
    }

    public static function getKey(string $keyName): string
    {
        return self::$params[$keyName];
    }

    private function fromSbToHttpsProtocol(string $connectionString): string
    {
        return str_replace('sb://', 'https://', $connectionString);
        // $connectionString = -strlen(explode(';', $connectionString)[3]);
    }
}
