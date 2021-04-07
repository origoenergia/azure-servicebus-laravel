<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Publisher;

use Exception;
use AzureServiceBus\ServiceBus\Internal\IServiceBus;
use AzureServiceBus\ServiceBus\Models\BrokeredMessage;
use OrigoEnergia\AzureServiceBusLaravel\Publisher\ProducerInterface;
use OrigoEnergia\AzureServiceBusLaravel\Utils\ErrorHandler;

class Producer implements ProducerInterface
{
    private IServiceBus $azureServiceBusClient;
    private BrokeredMessage $brokeredMessage;

    private ?string $topic;

    public function __construct(IServiceBus $azureServiceBusClient, ?string $topic = null)
    {
        $this->azureServiceBusClient = $azureServiceBusClient;
        $this->brokeredMessage = new BrokeredMessage();
        $this->topic = $topic;
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
        try {
            $this->azureServiceBusClient->sendTopicMessage($this->topic, $brokeredMessage);
        } catch (Exception $e) {
            ErrorHandler::handle($e, $this->azureServiceBusClient);
        }
    }
}
