<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Publisher;

use AzureServiceBus\ServiceBus\Internal\IServiceBus;
use AzureServiceBus\ServiceBus\Models\BrokeredMessage;
use OrigoEnergia\AzureServiceBusLaravel\Publisher\ProducerInterface;

class Producer implements ProducerInterface
{
    private IServiceBus $azureServiceBusClient;
    private BrokeredMessage $brokeredMessage;

    private ?string $topic;

    public function __construct(?string $topic = null, IServiceBus $azureServiceBusClient)
    {
        $this->topic = $topic;
        $this->brokeredMessage = new BrokeredMessage();
        $this->azureServiceBusClient = $azureServiceBusClient;
    }

    public function forTopic(?string $topic = null): self
    {
        $this->topic = $topic;
        return $this;
    }

    public function setTimeToLive(int $timeInMilliseconds = 60000): self
    {
        $this->brokeredMessage->setTimeToLive($timeInMilliseconds);
        return $this;
    }

    public function sendMessage(BrokeredMessage $brokeredMessage): void
    {
        $this->azureServiceBusClient->sendTopicMessage($this->topic, $brokeredMessage);
    }
}
