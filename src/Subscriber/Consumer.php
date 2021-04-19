<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Subscriber;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use AzureServiceBus\ServiceBus\Internal\IServiceBus;
use AzureServiceBus\ServiceBus\Models\BrokeredMessage;
use AzureServiceBus\ServiceBus\Models\SubscriptionInfo;
use AzureServiceBus\ServiceBus\Models\ReceiveMessageOptions;
use OrigoEnergia\AzureServiceBusLaravel\Utils\ErrorHandler;
use OrigoEnergia\AzureServiceBusLaravel\Destination\Message;

class Consumer implements ConsumerInterface
{
    private string $name;
    private ?string $destinationName;

    private IServiceBus $azureServiceBusClient;
    private Message $formattedMessage;

    public function __construct(IServiceBus $azureServiceBusClient, string $name, ?string $destinationName = null)
    {
        $this->azureServiceBusClient = $azureServiceBusClient;
        $this->name = $name;
        $this->destinationName = $destinationName;
        $this->formattedMessage = Message::create();
    }

    public function forTopic(?string $destinationName = null): self
    {
        $this->destinationName = $destinationName ?? $this->destinationName;

        $this->getOrCreate($destinationName);

        return $this;
    }

    public function receiveMessage(?string $consumerName = null, ?string $destinationName = null, ?ReceiveMessageOptions $options = null): ?self
    {
        $options = new ReceiveMessageOptions();

        $options->setReceiveAndDelete();

        try {
            $message = $this->azureServiceBusClient->receiveSubscriptionMessage($destinationName ?? $this->destinationName, $consumerName ?? $this->name, $options);
            $receivedMessage = $message != null && $message != '';

            if ($receivedMessage) {
                $this->formatReceivedMessage($message);
                return $this;
            } else {
                return null;
            }

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getMessage(): Message
    {
        return $this->formattedMessage;
    }

    public function getUnserializedMessage(): ShouldQueue
    {
        $decodedMessage = json_decode($this->formattedMessage->getBody());
        return unserialize($decodedMessage->data->command);
    }

    private function getOrCreate(): ?SubscriptionInfo
    {
        $subscriptionInfo = new SubscriptionInfo($this->name);
        $subscriptionInfo->setMaxDeliveryCount(4);
        $subscriptionInfo->setMessageCount(1);

        try {
            return $this->azureServiceBusClient->createSubscription($this->destinationName, $subscriptionInfo);
        } catch (Exception $e) {
            return ErrorHandler::handle($e, $this->azureServiceBusClient)->getSubscription($this->destinationName, $this->name);
        }
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
