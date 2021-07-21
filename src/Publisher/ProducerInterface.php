<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Publisher;

use AzureServiceBus\ServiceBus\Models\BrokeredMessage;
use OrigoEnergia\AzureServiceBusLaravel\Common\PubSubCommonInterface;

interface ProducerInterface extends PubSubCommonInterface
{
    public function forTopic(?string $topic = null): self;
    public function setTimeToLive(int $timeInMilliseconds = 60000): self;
    public function sendMessage(BrokeredMessage $brokeredMessage): void;
}
