<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Subscriber;

use AzureServiceBus\ServiceBus\Models\ReceiveMode;
use Illuminate\Contracts\Queue\ShouldQueue;
use OrigoEnergia\AzureServiceBusLaravel\Destination\Message;
use OrigoEnergia\AzureServiceBusLaravel\Common\PubSubCommonInterface;


interface ConsumerInterface extends PubSubCommonInterface
{
    public function receiveMessage(int $timeout = 0, int $mode = ReceiveMode::RECEIVE_AND_DELETE | ReceiveMode::PEEK_LOCK): ?self;
    public function getMessage(): Message;
    public function getUnserializedMessage(): ShouldQueue;
}
