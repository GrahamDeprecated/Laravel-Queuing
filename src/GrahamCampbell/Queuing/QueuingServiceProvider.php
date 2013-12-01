<?php namespace GrahamCampbell\Queuing;

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
 *
 * @package    Laravel-Queuing
 * @author     Graham Campbell
 * @license    Apache License
 * @copyright  Copyright 2013 Graham Campbell
 * @link       https://github.com/GrahamCampbell/Laravel-Queuing
 */

use Illuminate\Support\ServiceProvider;

class QueuingServiceProvider extends ServiceProvider {

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
    public function boot() {
        $this->package('graham-campbell/queuing');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app['jobprovider'] = $this->app->share(function($app) {
            return new Providers\JobProvider($app['config']);
        });
        $this->app['queuing'] = $this->app->share(function($app) {
            return new Classes\Queuing($app['queuing'], $app['jobprovider'], $app['config']['queue.default']);
        });
        $this->app['cron'] = $this->app->share(function($app) {
            return new Classes\Cron($app['queuing']);
        });

        $this->app['artisan']->add(new GrahamCampbell\Queuing\Commands\QueueLength);
        $this->app['artisan']->add(new GrahamCampbell\Queuing\Commands\QueueClear);
        $this->app['artisan']->add(new GrahamCampbell\Queuing\Commands\QueueIron);

        $this->app['artisan']->add(new GrahamCampbell\Queuing\Commands\CronStart);
        $this->app['artisan']->add(new GrahamCampbell\Queuing\Commands\CronStop);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return array('jobprovider', 'queuing', 'cron');
    }
}
