<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Context;

use AzureServiceBus\ServiceBus\Models\TopicInfo;
use AzureServiceBus\ServiceBus\Models\QueueInfo;
use AzureServiceBus\ServiceBus\Internal\IServiceBus;
use AzureServiceBus\ServiceBus\Models\QueueDescription;
use AzureServiceBus\ServiceBus\Models\SubscriptionDescription;
use AzureServiceBus\ServiceBus\Models\SubscriptionInfo;
use AzureServiceBus\ServiceBus\Models\TopicDescription;
use OrigoEnergia\AzureServiceBusLaravel\Publisher\Producer;
use OrigoEnergia\AzureServiceBusLaravel\Destination\Message;
use OrigoEnergia\AzureServiceBusLaravel\Subscriber\Consumer;
use OrigoEnergia\AzureServiceBusLaravel\Context\ContextInterface;

class AzureServiceBusContext extends BaseQueueContext implements ContextInterface
{
    private ?string $subscription;

    public function __construct(?string $subscription = null, ?string $topicOrQueue = null, IServiceBus $azureServiceBusClient)
    {
        parent::__construct($topicOrQueue, $azureServiceBusClient);

        $this->subscription = $subscription;
    }

    public function createProducer(?string $name = null): Producer
    {
        return new Producer($name ?? $this->topicOrQueue, $this->azureServiceBusClient);
    }

    public function createConsumer(?string $name = null): Consumer
    {
        $this->subscription = is_null($this->subscription) ? $name : $this->subscription;

        return new Consumer($this->subscription ?? $name, $this->topicOrQueue, $this->azureServiceBusClient);
    }

    public function createQueue(string $name, ?string $description = null): QueueInfo
    {
        $queueDescription = QueueDescription::create($description);
        $queue = new QueueInfo($name, $queueDescription);

        return $this->azureServiceBusClient->createQueue($queue);
    }

    public function deleteQueue(string $name): void
    {
        $this->azureServiceBusClient->deleteQueue($name);
    }

    public function createTopic(TopicInfo $topicInfo): TopicInfo
    {
        return $this->azureServiceBusClient->createTopic($topicInfo);
    }

    public function deleteTopic(string $name): void
    {
        $this->azureServiceBusClient->deleteTopic($name);
    }

    public function getTopic(string $name): TopicInfo
    {
        return $this->azureServiceBusClient->getTopic($name);
    }

    public function createSubscription(string $name, string $topic, ?string $description = null): SubscriptionInfo
    {
        $subscriptionInfo = new SubscriptionInfo($name, new SubscriptionDescription($description));
        return $this->azureServiceBusClient->createSubscription($topic, $subscriptionInfo);
    }

    public function deleteSubscription(string $name, string $topic): void
    {
        $this->azureServiceBusClient->deleteSubscription($topic, $name);
    }

    public function createMessage(): Message
    {
        return Message::create();
    }

    public function createTopicWithGoodPerformance(string $name, string $subscription, ?string $description = null): TopicInfo
    {
        $topicDescription = $description ? TopicDescription::create($description) : null;
        $topicInfo = new TopicInfo($name ?? $this->topicOrQueue, $topicDescription);

        $topicInfo->setRequiresDuplicateDetection(false);
        $topicInfo->setEnableBatchedOperations(false);
        $topicInfo->setDefaultMessageTimeToLive('P1D');
        $topicInfo->setMaxSizeInMegabytes(5120);

        return $this->createTopic($topicInfo);

        // $topicInfo->setLockDuration(env('AZURE_SERVICE_BUS_TOPIC_SUBSCRIPTION_LOCK_DURATION') ?? 10);

        // $topicInfo->setMaxDeliveryCount(env('AZURE_SERVICE_BUS_SUBSCRIPTION_MAX_DELIVERY_COUNT') ?? 10);
    }
}
