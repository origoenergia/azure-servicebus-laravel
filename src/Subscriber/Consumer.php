<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Subscriber;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use AzureServiceBus\ServiceBus\Internal\IServiceBus;
use AzureServiceBus\ServiceBus\Models\BrokeredMessage;
use AzureServiceBus\ServiceBus\Models\SubscriptionInfo;
use AzureServiceBus\ServiceBus\Models\ReceiveMessageOptions;
use AzureServiceBus\ServiceBus\Models\ReceiveMode;
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

    public function forSubscription(string $subscription): self
    {
        $this->name = $subscription;
        return $this;
    }

    public function receiveMessage(int $timeout = 0, int $mode = ReceiveMode::RECEIVE_AND_DELETE | ReceiveMode::PEEK_LOCK): ?self
    {
        $options = new ReceiveMessageOptions();

        $options->setTimeout($timeout);
        $options->setReceiveMode($mode);

        try {
            $message = $this->azureServiceBusClient->receiveSubscriptionMessage($destinationName ?? $this->destinationName, $this->name, $options);
            $receivedMessage = $message != null || $message != '';

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
