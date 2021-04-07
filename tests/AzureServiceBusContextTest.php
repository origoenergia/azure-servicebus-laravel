<?php

namespace OrigoEnergia\Tests\AzureServiceBusLaravel;

use Tests\TestCase;
use Illuminate\Support\Str;
use AzureServiceBus\ServiceBus\Models\TopicInfo;
use AzureServiceBus\ServiceBus\Models\QueueInfo;
use AzureServiceBus\ServiceBus\Internal\IServiceBus;
use AzureServiceBus\ServiceBus\Models\BrokeredMessage;
use AzureServiceBus\ServiceBus\Models\SubscriptionInfo;
use OrigoEnergia\AzureServiceBusLaravel\Publisher\Producer;
use OrigoEnergia\AzureServiceBusLaravel\Subscriber\Consumer;
use OrigoEnergia\AzureServiceBusLaravel\Context\ContextInterface;

class AzureServiceBusContextTest extends TestCase
{
    public function test_should_create_consumer()
    {
        $this->assertInstanceOf(Consumer::class, $this->createConsumer());
    }

    public function test_should_create_producer()
    {
        $this->assertInstanceOf(Producer::class, $this->createProducer());
    }

    public function test_should_create_queue()
    {
        $contextMock = $this->createContextMock();
        $createdQueue = $contextMock->createQueue(Str::random());

        $this->assertNotNull($createdQueue);
        $this->assertInstanceOf(QueueInfo::class, $createdQueue);
    }

    public function test_should_create_topic()
    {
        $contextMock = $this->createContextMock();
        $createdTopic = $contextMock->createTopic(Str::random(),);

        $this->assertNotNull($createdTopic);
        $this->assertInstanceOf(TopicInfo::class, $createdTopic);
    }

    public function test_should_create_subscription()
    {
        $contextMock = $this->createContextMock();
        $createdSubscription = $contextMock->createSubscription(Str::random(), Str::random());

        $this->assertNotNull($createdSubscription);
        $this->assertInstanceOf(SubscriptionInfo::class, $createdSubscription);
    }

    public function test_should_create_message()
    {
        $contextMock = $this->createContextMock();
        $createdMessage = $contextMock->createMessage()->setBody(Str::random())->build();

        $this->assertNotNull($createdMessage);
        $this->assertInstanceOf(BrokeredMessage::class, $createdMessage);
    }

    public function test_shoud_delete_queue()
    {
        $contextMock = $this->createContextMock();

        $this->assertNull($contextMock->deleteQueue(Str::random()));
    }

    public function test_shoud_delete_topic()
    {
        $contextMock = $this->createContextMock();

        $this->assertNull($contextMock->deleteTopic(Str::random()));
    }

    public function test_shoud_delete_subscription()
    {
        $contextMock = $this->createContextMock();

        $this->assertNull($contextMock->deleteSubscription(Str::random(), Str::random()));
    }

    private function createConsumer(): Consumer
    {
        return new Consumer($this->createRestProxyMock(), Str::random(15), Str::random(15));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|IServiceBus
     */
    private function createRestProxyMock()
    {
        return $this->createMock(IServiceBus::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ContextInterface
     */
    private function createContextMock()
    {
        return $this->createMock(ContextInterface::class);
    }

    private function createProducer(): Producer
    {
        return new Producer($this->createRestProxyMock());
    }
}
