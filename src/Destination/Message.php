<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Destination;

use AzureServiceBus\ServiceBus\Models\BrokeredMessage;
use AzureServiceBus\ServiceBus\Models\BrokerProperties;

class Message
{
    private BrokeredMessage $brokeredMessage;

    private function __construct()
    {
        $this->reset();
    }

    public static function create(): self
    {
        return new self();
    }

    public function getBrokeredMessage(): BrokeredMessage
    {
        return $this->brokeredMessage;
    }

    public function setBrokeredMessage(BrokeredMessage $brokeredMessage): self
    {
        $this->brokeredMessage = $brokeredMessage;
        return $this;
    }

    public function getContentType(): string
    {
        return $this->brokeredMessage->getContentType();
    }

    public function setContentType(string $contentType): self
    {
        $this->brokeredMessage->setContentType($contentType);
        return $this;
    }

    public function getMessageText(): string
    {
        $messageContent = [
            'body' => $this->getBody(),
            'properties' => $this->getProperties(),
        ];

        return base64_encode(json_encode($messageContent));
    }

    public function getTimeToLive(): ?int
    {
        return $this->brokeredMessage->getTimeToLive();
    }

    public function setTimeToLive(?int $timeToLive = null): self
    {
        $this->brokeredMessage->setTimeToLive($timeToLive);
        return $this;
    }

    public function getBody(): string
    {
        return $this->brokeredMessage->getBody();
    }

    public function setBody(string $body): self
    {
        $this->brokeredMessage->setBody($body);
        return $this;
    }

    public function setProperties(array $jsonProperties): self
    {
        $this->brokeredMessage->setBrokerProperties(BrokerProperties::create(json_encode($jsonProperties)));
        return $this;
    }

    public function setProperty(string $name, $value): self
    {
        if (null === $value) {
            return $this;
            unset($this->brokeredMessage->getBrokerProperties()[$name]);
        } else {
            $this->brokeredMessage->setProperty($name, $value);
        }
    }

    public function getProperties(): BrokerProperties
    {
        return $this->brokeredMessage->getBrokerProperties();
    }

    public function setCorrelationId(string $correlationId): self
    {
        $this->brokeredMessage->setCorrelationId($correlationId);
        return $this;
    }

    public function getCorrelationId(): ?string
    {
        return $this->brokeredMessage->getCorrelationId();
    }
    
    public function setLabel(string $label): self
    {
        $this->brokeredMessage->setLabel($label);
        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->brokeredMessage->getLabel();
    }

    public function setMessageId($messageId): self
    {
        $this->brokeredMessage->setMessageId($messageId);
        return $this;
    }

    public function getMessageId(): ?string
    {
        return $this->brokeredMessage->getMessageId();
    }

    public function setReplyTo(string $to): self
    {
        $this->brokeredMessage->setReplyTo($to);
        return $this;
    }

    public function getReplyTo(): ?string
    {
        return $this->brokeredMessage->getReplyTo();
    }

    public function reset(): void
    {
        $this->brokeredMessage = new BrokeredMessage();
        $this->redelivered = false;
    }

    public function build(): BrokeredMessage
    {
        $brokeredMessage = $this->brokeredMessage;
        $this->reset();

        return $brokeredMessage;
    }
}
