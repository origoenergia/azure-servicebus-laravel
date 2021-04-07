<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Context;

use AzureServiceBus\ServiceBus\Models\QueueInfo;
use AzureServiceBus\ServiceBus\Models\SubscriptionInfo;
use AzureServiceBus\ServiceBus\Models\TopicInfo;
use OrigoEnergia\AzureServiceBusLaravel\Subscriber\Consumer;
use OrigoEnergia\AzureServiceBusLaravel\Destination\Message;
use OrigoEnergia\AzureServiceBusLaravel\Publisher\Producer;

interface ContextInterface
{
    public function createProducer(?string $name = null): Producer;
    public function createConsumer(?string $name = null): Consumer;

    public function createQueue(string $name, ?string $description = null): QueueInfo;
    public function deleteQueue(string $name): void;

    public function createTopic(string $name, ?string $description = null): TopicInfo;
    public function deleteTopic(string $name): void;
    public function getTopic(string $name): TopicInfo;

    public function createSubscription(string $name, string $topic, ?string $description = null): SubscriptionInfo;
    public function deleteSubscription(string $name, string $topic): void;

    public function createMessage(): Message;
}
