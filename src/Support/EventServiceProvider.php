<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Support;

use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use OrigoEnergia\AzureServiceBusLaravel\SubscriptionTopicListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // Registered::class => [
        //     SubscriptionTopicListener::class,
        // ],
        // SubscriptionTopicListener::class
    ];

    protected $subscribe = [
        SubscriptionTopicListener::class
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        // parent::boot();
    }
}
