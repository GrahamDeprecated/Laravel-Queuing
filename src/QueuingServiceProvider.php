<?php

/**
 * This file is part of Laravel Queuing by Graham Campbell.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace GrahamCampbell\Queuing;

use ReflectionClass;
use Illuminate\Support\ServiceProvider;

/**
 * This is the queuing service provider class.
 *
 * @package    Laravel-Queuing
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Queuing/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Queuing
 */
class QueuingServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('graham-campbell/queuing', 'graham-campbell/queuing', __DIR__);

        $this->commands('command.queueiron');

        include __DIR__.'/routes.php';

        // process jobs on shutdown
        $this->app->shutdown(function ($app) {
            foreach (array_keys($app['queue']->getConnections()) as $name) {
                $app['queue']->connection($name)->process();
            }
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerManager();
        $this->registerIronCommand();
    }

    /**
     * Register the queue manager.
     *
     * @return void
     */
    protected function registerManager()
    {
        $this->app->bindShared('queue', function($app)
        {
            $manager = new Managers\QueueManager($app);

            $this->registerConnectors($manager);

            return $manager;
        });
    }

    /**
     * Register the connectors on the queue manager.
     *
     * @param  \Illuminate\Queue\QueueManager  $manager
     * @return void
     */
    public function registerConnectors($manager)
    {
        foreach (array('Sync', 'Beanstalkd', 'Redis', 'Sqs', 'Iron') as $connector) {
            $this->{"register{$connector}Connector"}($manager);
        }
    }

    /**
     * Register the Sync queue connector.
     *
     * @param  \Illuminate\Queue\QueueManager  $manager
     * @return void
     */
    protected function registerSyncConnector($manager)
    {
        $manager->addConnector('sync', function() {
            return new Connectors\SyncConnector();
        });
    }

    /**
     * Register the Beanstalkd queue connector.
     *
     * @param  \Illuminate\Queue\QueueManager  $manager
     * @return void
     */
    protected function registerBeanstalkdConnector($manager)
    {
        $manager->addConnector('beanstalkd', function() {
            return new Connectors\BeanstalkdConnector();
        });
    }

    /**
     * Register the Redis queue connector.
     *
     * @param  \Illuminate\Queue\QueueManager  $manager
     * @return void
     */
    protected function registerRedisConnector($manager)
    {
        $app = $this->app;

        $manager->addConnector('redis', function() use ($app) {
            $redis = $app['redis'];

            return new Connectors\RedisConnector($redis);
        });
    }

    /**
     * Register the Amazon SQS queue connector.
     *
     * @param  \Illuminate\Queue\QueueManager  $manager
     * @return void
     */
    protected function registerSqsConnector($manager)
    {
        $manager->addConnector('sqs', function() {
            return new Connectors\SqsConnector();
        });
    }

    /**
     * Register the IronMQ queue connector.
     *
     * @param  \Illuminate\Queue\QueueManager  $manager
     * @return void
     */
    protected function registerIronConnector($manager)
    {
        $app = $this->app;

        $manager->addConnector('iron', function() use ($app) {
            $encrypter = $app['encrypter'];
            $request = $app['request'];

            return new Connectors\IronConnector($encrypter, $request);
        });

        $this->registerIronRequestBinder();
    }

    /**
     * Register the request rebinding event for the Iron queue.
     *
     * @return void
     */
    protected function registerIronRequestBinder()
    {
        $this->app->rebinding('request', function($app, $request) {
            if ($app['queue']->connected('iron')) {
                $app['queue']->connection('iron')->setRequest($request);
            }
        });
    }

    /**
     * Register the iron command class.
     *
     * @return void
     */
    protected function registerIronCommand()
    {
        $this->app->bindShared('command.queueiron', function ($app) {
            return new Commands\QueueIron();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array(
            'queue',
            'command.queueiron'
        );
    }
}
