<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Subscriber;

use Illuminate\Contracts\Queue\ShouldQueue;
use AzureServiceBus\ServiceBus\Models\ReceiveMessageOptions;
use OrigoEnergia\AzureServiceBusLaravel\Destination\Message;
use OrigoEnergia\AzureServiceBusLaravel\Common\PubSubCommonInterface;


interface ConsumerInterface extends PubSubCommonInterface
{
    public function receiveMessage(?string $consumerName = null, ?string $destinationName = null, ?ReceiveMessageOptions $options = null): ?self;
    public function getMessage(): Message;
    public function getUnserializedMessage(): ShouldQueue;
}
