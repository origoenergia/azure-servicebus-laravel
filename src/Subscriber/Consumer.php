<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Subscriber;

use AzureServiceBus\ServiceBus\Internal\IServiceBus;
use AzureServiceBus\ServiceBus\Models\BrokeredMessage;
use AzureServiceBus\ServiceBus\Models\ReceiveMessageOptions;
use OrigoEnergia\AzureServiceBusLaravel\Destination\Message;

class Consumer implements ConsumerInterface
{
    private string $name;
    private ?string $topic;

    private IServiceBus $azureServiceBusClient;
    private Message $formattedMessage;

    public function __construct(string $name, ?string $topic = null, IServiceBus $azureServiceBusClient)
    {
        $this->azureServiceBusClient = $azureServiceBusClient;
        $this->name = $name;
        $this->topic = $topic;
        $this->formattedMessage = Message::create();
    }

    public function forTopic(?string $topic = null): self
    {
        $this->topic = $topic ?? $this->topic;
        return $this;
    }

    public function fromSubscription(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function receiveMessage(?ReceiveMessageOptions $receiveOptions = null): self
    {
        if (is_null($receiveOptions)) {
            $receiveOptions = new ReceiveMessageOptions();
            $receiveOptions->setPeekLock();
            $receiveOptions->setTimeout(0);
        }

        $message = $this->azureServiceBusClient->receiveSubscriptionMessage($topic ?? $this->topic, $this->name, $receiveOptions);

        if ($message) {
            $this->formatReceivedMessage($message);
        }

        return $this;
    }

    public function receiveMessageAndDelete(): self
    {
        $receiveOptions = new ReceiveMessageOptions();
        $receiveOptions->setReceiveAndDelete();
        $receiveOptions->setTimeout(0);

        $this->receiveMessage($receiveOptions);

        return $this;
    }

    public function getMessage(): Message
    {
        return $this->formattedMessage;
    }

    public function decode()
    {
        return json_decode($this->formattedMessage->getBody());
    }

    private function formatReceivedMessage(BrokeredMessage $message)
    {
        $messageBody = $message->getBody();
        $messageProperties = $message->getProperties();

        $this->formattedMessage = Message::create();

        $this->formattedMessage->setBody($messageBody);
        $this->formattedMessage->setProperties($messageProperties);
        $this->formattedMessage->setMessageId($message->getMessageId());
        $this->formattedMessage->setBrokeredMessage($message);
    }
}
