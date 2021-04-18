<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Publisher;

use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Queue\Jobs\Job;
use AzureServiceBus\ServiceBus\Internal\IServiceBus;
use AzureServiceBus\ServiceBus\Models\BrokeredMessage;

class AzureServiceBusJob extends Job implements JobContract
{
    /**
     * The Azure IServiceBus instance.
     *
     * @var \AzureServiceBus\ServiceBus\Internal\IServiceBus
     */
    protected IServiceBus $azure;

    /**
     * The Azure ServiceBus job instance.
     *
     * @var \AzureServiceBus\ServiceBus\Models\BrokeredMessage
     */
    protected BrokeredMessage $job;

    /**n
     * The raw payload on the queue.
     *
     * @var string
     */
    protected string $rawMessage;
    /**
     * Create a new job instance.
     *
     * @param \Illuminate\Container\Container $container
     * @param \AzureServiceBus\ServiceBus\Internal\IServiceBus $azure
     * @param \AzureServiceBus\ServiceBus\Models\BrokeredMessage $job
     * @param string $queue
     *
     * @return \OrigoEnergia\AzureServiceBusLaravelTopic\AzureJob
     */
    public function __construct(Container $container, IServiceBus $azure, BrokeredMessage $job, string $queue, string $rawMessage)
    {
        $this->azure = $azure;
        $this->job = $job;
        $this->queue = $queue;
        $this->container = $container;
        $this->rawMessage = $rawMessage;
    }

    /**
     * Delete the job from the queue.
     */
    public function delete()
    {
        parent::delete();
        $this->azure->deleteMessage($this->job);
    }

    /**
     * Release the job back into the queue.
     *
     * @param int $delay
     */
    public function release($delay = 0)
    {
        $release = new \DateTime;
        $release->setTimezone(new \DateTimeZone('UTC'));
        $release->add(new \DateInterval('PT' . $delay . 'S'));
        $this->job->setScheduledEnqueueTimeUtc($release);
        $this->azure->unlockMessage($this->job);
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts(): int
    {
        return $this->job->getDeliveryCount();
    }

    /**
     * Get the job identifier.
     *
     * @return string
     */
    public function getJobId(): string
    {
        return $this->job->getMessageId();
    }

    /**
     * Get the underlying Azure client instance.
     *
     * @return \AzureServiceBus\ServiceBus\Internal\IServiceBus
     */

    public function getAzure(): IServiceBus
    {
        return $this->azure;
    }

    /**
     * Get the underlying raw Azure job.
     *
     * @return \AzureServiceBus\ServiceBus\Models\BrokeredMessage
     */

    public function getAzureJob(): BrokeredMessage
    {
        return $this->job;
    }

    /**
     * Get the raw body string for the job.
     *
     * @return string
     */
    public function getRawBody(): string
    {
        return $this->rawMessage;
    }
}
