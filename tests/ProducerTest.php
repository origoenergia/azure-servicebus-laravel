<?php

namespace OrigoEnergia\Tests\AzureServiceBusLaravel;

use Tests\TestCase;
use Illuminate\Support\Str;
use AzureServiceBus\ServiceBus\Internal\IServiceBus;
use AzureServiceBus\ServiceBus\Models\BrokeredMessage;
use OrigoEnergia\AzureServiceBusLaravel\Publisher\Producer;
use OrigoEnergia\AzureServiceBusLaravel\Context\ContextInterface;
use OrigoEnergia\AzureServiceBusLaravel\Publisher\ProducerInterface;

class ProducerTest extends TestCase
{
    public function test_should_send_message_to_topic()
    {
        $producer = $this->createProducer();
        $contextMock = $this->createContextMock();
        $message = $contextMock->createMessage()->setBody(Str::random(15))->build();

        $this->assertNull($producer->sendMessage($message));
        $this->assertInstanceOf(BrokeredMessage::class, $message);
        $this->assertInstanceOf(ProducerInterface::class, $producer);
        $this->assertInstanceOf(Producer::class, $producer);
    }

    private function createProducer(): Producer
    {
        return new Producer($this->createRestProxyMock());
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
}
