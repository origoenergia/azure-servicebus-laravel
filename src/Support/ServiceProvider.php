<?php

namespace OrigoEnergia\AzureServiceBusLaravel\Support;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Queue;
use OrigoEnergia\AzureServiceBusLaravel\Connection\AzureServiceBusConnector;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $manager = $this->app['queue'];
        $this->registerConnector($manager);
    }

    public function provides(): array
    {
        return ['azureservicebus'];
    }

    public function register()
    {
        App::bind('azureservicebus', function () {
            return Queue::connection('azureservicebus');
        });
    }

    private function registerConnector($manager)
    {
        $manager->addConnector('azureservicebus', function () {
            return new AzureServiceBusConnector();
        });
    }
}
