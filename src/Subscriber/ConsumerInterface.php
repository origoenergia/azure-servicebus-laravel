<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Subscriber;

use AzureServiceBus\ServiceBus\Models\ReceiveMessageOptions;
use OrigoEnergia\AzureServiceBusLaravel\Destination\Message;
use OrigoEnergia\AzureServiceBusLaravel\Common\PubSubCommonInterface;

interface ConsumerInterface extends PubSubCommonInterface
{
    public function receiveMessage(ReceiveMessageOptions $receiveOptions): self;
    public function receiveMessageAndDelete(): self;
    public function forTopic(?string $topic = null): self;
    public function fromSubscription(string $subscription): self;
    public function getMessage(): Message;
    public function decode();
}
