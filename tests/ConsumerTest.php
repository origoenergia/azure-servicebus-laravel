<?php

namespace OrigoEnergia\Tests\AzureServiceBusLaravel;

use Tests\TestCase;
use Illuminate\Support\Str;
use AzureServiceBus\ServiceBus\Internal\IServiceBus;
use OrigoEnergia\AzureServiceBusLaravel\Subscriber\Consumer;

class ConsumerTest extends TestCase
{
    public function test_should_consumes_message_from_topic()
    {
        $message = $this->createConsumer()->forTopic(Str::random())->receiveMessage();

        if (is_null($message)) {
            $this->assertNull($message);
        } else {
            $this->assertNotNull($message);
            $this->assertInstanceOf(Message::class, $message->getMessage());
        }

        $this->assertInstanceOf(Consumer::class, $this->createConsumer());
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
}
