<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Common;

use Illuminate\Queue\Queue;
use Illuminate\Queue\Jobs\Job;
use AzureServiceBus\ServiceBus\Internal\IServiceBus;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use AzureServiceBus\ServiceBus\Models\BrokeredMessage;
use AzureServiceBus\ServiceBus\Models\ReceiveMessageOptions;
use OrigoEnergia\AzureServiceBusLaravel\Pub\AzureServiceBusJob;

abstract class AbstractBaseAzureServiceBus extends Queue implements QueueContract
{
    /**
     * The Azure IServiceBus instance.
     *
     * @var \AzureServiceBus\ServiceBus\Internal\IServiceBus
     */
    protected IServiceBus $azure;

    /**
     * The name of the default topic.
     *
     * @var string
     */
    protected string $default;

    /**
     * The options to set PeekAndLock.
     *
     * @var ReceiveMessageOptions
     */
    protected ReceiveMessageOptions $receiveOptions;

    /**
     * Create a new Azure IQueue queue instance.
     *
     * @param \AzureServiceBus\ServiceBus\Internal\IServiceBus $azure
     * @param string                                        $default
     *
     * @return \OrigoEnergia\AzureServiceBusLaravelTopic\AzureQueue
     */
    public function __construct(IServiceBus $azure, string $default)
    {
        $this->azure = $azure;
        $this->default = $default;
        $this->receiveOptions = new ReceiveMessageOptions();
        $this->receiveOptions->setPeekLock();
    }

    protected function sendInternal(string $queue, BrokeredMessage $message): void
    {
        $this->azure->sendQueueMessage($queue, $message);
    }

    protected function receiveInternal(string $queue, ReceiveMessageOptions $receiveOptions): ?BrokeredMessage
    {
        return $this->azure->receiveQueueMessage($queue, $receiveOptions);
    }

    public function size($queue = null)
    {
        return 0;
    }

    /**
     * Push a new job onto the queue.
     *
     * @param string $job
     * @param mixed  $data
     * @param string $queue
     */
    public function push($job, $data = '', $queue = null): void
    {
        $this->pushRaw($this->createPayload($job, $queue, $data), $queue);
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param string $payload
     * @param string $queue
     * @param array  $options
     *
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = array()): void
    {
        if (is_object($payload)) {
            $payload = json_encode($payload);
        }
        $message = new BrokeredMessage($payload);
        $this->sendInternal($this->getQueue($queue), $message);
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param int    $delay
     * @param string $job
     * @param mixed  $data
     * @param string $queue
     */

    public function later($delay, $job, $data = '', $queue = null): void
    {
        $payload = $this->createPayload($job, $queue, $data);
        $release = new \DateTime;
        $release->setTimezone(new \DateTimeZone('UTC'));
        $release->add(new \DateInterval('PT' . $delay . 'S'));
        $message = new BrokeredMessage($payload);
        $message->setScheduledEnqueueTimeUtc($release);
        $this->sendInternal($this->getQueue($queue), $message);
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param string $queue
     *
     * @return \Illuminate\Queue\Jobs\Job|null
     */
    public function pop($queue = null): ?Job
    {
        $popped = $this->receiveInternal($this->getQueue($queue), $this->receiveOptions);
        if (empty($popped)) {
            return null;
        }
        $rawMessage = $popped->getBody()->getContents();
        return new AzureServiceBusJob($this->container, $this->azure, $popped, $this->getQueue($queue), $rawMessage);
    }

    /**
     * Get the queue or return the default.
     *
     * @param string|null $queue
     *
     * @return string
     */
    public function getQueue($queue): ?string
    {
        return $queue ?: $this->default;
    }

    /**
     * Get the underlying Azure IQueue instance.
     *
     * @return \AzureServiceBus\Queue\Internal\IQueue
     */
    public function getAzure(): IServiceBus
    {
        return $this->azure;
    }
}
