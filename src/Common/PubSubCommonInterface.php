<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Common;

interface PubSubCommonInterface
{
    public function forTopic(?string $name = null): self;
}
