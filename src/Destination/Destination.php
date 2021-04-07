<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Destination;

class Destination
{
    protected string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQueueName(): string
    {
        return $this->name;
    }

    public function getTopicName(): string
    {
        return $this->name;
    }
}
