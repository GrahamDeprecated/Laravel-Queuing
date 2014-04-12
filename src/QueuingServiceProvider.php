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

        include __DIR__.'/routes.php';
        include __DIR__.'/listeners.php';

        // process jobs on shutdown
        $this->app->shutdown(function ($app) {
            $app['queuing']->process();
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerJobProvider();
        $this->registerQueuing();
        $this->registerCron();

        $this->registerQueueLengthCommand();
        $this->registerQueueClearCommand();
        $this->registerQueueIronCommand();
        $this->registerCronStartCommand();
        $this->registerCronStopCommand();

        if ($app['config']['graham-campbell/core::commands']) {
            $this->registerCommandSubscriber();
        }
    }

    /**
     * Register the job provider class.
     *
     * @return void
     */
    protected function registerJobProvider()
    {
        $this->app->bindShared('jobprovider', function ($app) {
            $model = $app['config']['graham-campbell/queuing::job'];
            $job = new $model();

            $config = $app['config'];

            return new Providers\JobProvider($job, $config);
        });
    }

    /**
     * Register the queuing class.
     *
     * @return void
     */
    protected function registerQueuing()
    {
        $this->app->bindShared('queuing', function ($app) {
            $queue = $app['queue'];
            $jobprovider = $app['jobprovider'];
            $driver = $app['config']['queue.default'];

            return new Classes\Queuing($queue, $jobprovider, $driver);
        });
    }

    /**
     * Register the cron class.
     *
     * @return void
     */
    protected function registerCron()
    {
        $this->app->bindShared('cron', function ($app) {
            $queuing = $app['queuing'];

            return new Classes\Cron($queuing);
        });
    }

    /**
     * Register the queue length command class.
     *
     * @return void
     */
    protected function registerQueueLengthCommand()
    {
        $this->app->bindShared('command.queuelength', function ($app) {
            return new Commands\QueueLength();
        });

        $this->commands('command.queuelength');
    }

    /**
     * Register the queue clear command class.
     *
     * @return void
     */
    protected function registerQueueClearCommand()
    {
        $this->app->bindShared('command.queueclear', function ($app) {
            return new Commands\QueueClear();
        });

        $this->commands('command.queueclear');
    }

    /**
     * Register the queue iron command class.
     *
     * @return void
     */
    protected function registerQueueIronCommand()
    {
        $this->app->bindShared('command.queueiron', function ($app) {
            return new Commands\QueueIron();
        });

        $this->commands('command.queueiron');
    }

    /**
     * Register the cron start command class.
     *
     * @return void
     */
    protected function registerCronStartCommand()
    {
        $this->app->bindShared('command.cronstart', function ($app) {
            return new Commands\CronStart();
        });

        $this->commands('command.cronstart');
    }

    /**
     * Register the cron stop command class.
     *
     * @return void
     */
    protected function registerCronStopCommand()
    {
        $this->app->bindShared('command.cronstop', function ($app) {
            return new Commands\CronStop();
        });

        $this->commands('command.cronstop');
    }

    /**
     * Register the command subscriber class.
     *
     * @return void
     */
    protected function registerCommandSubscriber()
    {
        $this->app->bindShared('GrahamCampbell\Queuing\Subscribers\CommandSubscriber', function ($app) {
            $config = $app['config'];

            return new Subscribers\CommandSubscriber($config);
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
            'jobprovider',
            'queuing',
            'cron',
            'command.queuelength',
            'command.queueclear',
            'command.queueiron',
            'command.cronstart',
            'command.cronstop'
        );
    }
}
