<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Context;

use Exception;
use AzureServiceBus\ServiceBus\Models\TopicInfo;
use AzureServiceBus\ServiceBus\Models\QueueInfo;
use AzureServiceBus\ServiceBus\Internal\IServiceBus;
use AzureServiceBus\ServiceBus\Models\QueueDescription;
use AzureServiceBus\ServiceBus\Models\SubscriptionDescription;
use AzureServiceBus\ServiceBus\Models\SubscriptionInfo;
use AzureServiceBus\ServiceBus\Models\TopicDescription;
use OrigoEnergia\AzureServiceBusLaravel\Publisher\Producer;
use OrigoEnergia\AzureServiceBusLaravel\Utils\ErrorHandler;
use OrigoEnergia\AzureServiceBusLaravel\Destination\Message;
use OrigoEnergia\AzureServiceBusLaravel\Subscriber\Consumer;
use OrigoEnergia\AzureServiceBusLaravel\Context\ContextInterface;

class AzureServiceBusContext extends BaseQueueContext implements ContextInterface
{
    private string $subscriptionName;

    public function __construct(IServiceBus $azureServiceBusClient, string $destinationName, string $subscriptionName)
    {
        parent::__construct($azureServiceBusClient, $destinationName);

        $this->subscriptionName = $subscriptionName;
    }

    public function createQueue(string $name, ?string $description = null): QueueInfo
    {
        $queueInfo = $this->defineDestionationDefaultConfigurations($name, $description, 'queue');

        try {
            return $this->azureServiceBusClient->createQueue($queueInfo);
        } catch (Exception $e) {
            ErrorHandler::handle($e);
            return $this->getQueue($name ?? $this->destinationName);
        }
    }

    public function deleteQueue(string $name): void
    {
        $this->azureServiceBusClient->deleteQueue($name);
    }

    public function createTopic(string $name, ?string $description = null): TopicInfo
    {
        $topicInfo = $this->defineDestionationDefaultConfigurations($name, $description, 'topic');

        try {
            return $this->azureServiceBusClient->createTopic($topicInfo);
        } catch (Exception $e) {
            return ErrorHandler::handle($e, $this->azureServiceBusClient)->getTopic($name ?? $this->destinationName);
        }
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

    public function createProducer(?string $name = null): Producer
    {
        return new Producer($this->azureServiceBusClient, $name ?? $this->destinationName);
    }

    public function createConsumer(?string $name = null): Consumer
    {
        return new Consumer($this->azureServiceBusClient, $this->subscriptionName ?? $name, $this->destinationName);
    }

    public function createMessage(): Message
    {
        return Message::create();
    }

    private function defineDestionationDefaultConfigurations(string $name, ?string $description = null, ?string $type = null)
    {
        $destinationDescription = null;
        $destinationInfo = null;

        switch ($type) {
            case 'topic':
                $destinationDescription = $description ? TopicDescription::create($description) : null;
                $destinationInfo = new TopicInfo($name ?? $this->destinationName, $destinationDescription);
                break;
            case 'queue':
                $destinationDescription = QueueDescription::create($description);
                $destinationInfo = new QueueInfo($name ?? $this->destinationName, $destinationDescription);
                break;
        }

        // Slightly improves the performance of consumption and messaging
        $destinationInfo->setRequiresDuplicateDetection(env('AZURE_SERVICE_BUS_TOPIC_REQUIRES_DUPLICATE_DETECTION') ?? false);
        $destinationInfo->setEnableBatchedOperations(env('AZURE_SERVICE_BUS_TOPIC_ENABLE_BATCHED_OPERATIONS') ?? false);
        $destinationInfo->setLockDuration(env('AZURE_SERVICE_BUS_TOPIC_SUBSCRIPTION_LOCK_DURATION') ?? 10);

        $destinationInfo->setDefaultMessageTimeToLive(env('AZURE_SERVICE_BUS_TOPIC_DEFAULT_TIME_TO_LIVE') ?? 'P1D');
        $destinationInfo->setMaxSizeInMegabytes(env('AZURE_SERVICE_BUS_TOPIC_MAX_SIZE_MB') ?? 5120);
        $destinationInfo->setMaxDeliveryCount(env('AZURE_SERVICE_BUS_SUBSCRIPTION_MAX_DELIVERY_COUNT') ?? 10);

        return $destinationInfo;
    }
}
