<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Publisher;

use AzureServiceBus\ServiceBus\Models\BrokeredMessage;
use OrigoEnergia\AzureServiceBusLaravel\Common\PubSubCommonInterface;

interface ProducerInterface extends PubSubCommonInterface
{
    public function sendMessage(BrokeredMessage $brokeredMessage): void;
}
