<?php

/*
 * This file is part of Laravel Queuing.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Queuing;

use Illuminate\Queue\QueueServiceProvider;

/**
 * This is the queuing service provider class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class QueuingServiceProvider extends QueueServiceProvider
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

        $this->app->shutdown(function ($app) {
            $app['queue']->processAll();
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();
        $this->registerIronCommand();
    }

    /**
     * Register the queue manager.
     *
     * @return void
     */
    protected function registerManager()
    {
        $this->app->bindShared('queue', function ($app) {
            $manager = new QueueManager($app);

            $this->registerConnectors($manager);

            return $manager;
        });
    }

    /**
     * Register the Sync queue connector.
     *
     * @param \Illuminate\Queue\QueueManager $manager
     *
     * @return void
     */
    protected function registerSyncConnector($manager)
    {
        $manager->addConnector('sync', function () {
            return new Connectors\SyncConnector();
        });
    }

    /**
     * Register the Beanstalkd queue connector.
     *
     * @param \Illuminate\Queue\QueueManager $manager
     *
     * @return void
     */
    protected function registerBeanstalkdConnector($manager)
    {
        $manager->addConnector('beanstalkd', function () {
            return new Connectors\BeanstalkdConnector();
        });
    }

    /**
     * Register the Redis queue connector.
     *
     * @param \Illuminate\Queue\QueueManager $manager
     *
     * @return void
     */
    protected function registerRedisConnector($manager)
    {
        $app = $this->app;

        $manager->addConnector('redis', function () use ($app) {
            $redis = $app['redis'];

            return new Connectors\RedisConnector($redis);
        });
    }

    /**
     * Register the Amazon SQS queue connector.
     *
     * @param \Illuminate\Queue\QueueManager $manager
     *
     * @return void
     */
    protected function registerSqsConnector($manager)
    {
        $manager->addConnector('sqs', function () {
            return new Connectors\SqsConnector();
        });
    }

    /**
     * Register the IronMQ queue connector.
     *
     * @param \Illuminate\Queue\QueueManager $manager
     *
     * @return void
     */
    protected function registerIronConnector($manager)
    {
        $app = $this->app;

        $manager->addConnector('iron', function () use ($app) {
            $encrypter = $app['encrypter'];
            $request = $app['request'];

            return new Connectors\IronConnector($encrypter, $request);
        });

        $this->registerIronRequestBinder();
    }

    /**
     * Register the iron command class.
     *
     * @return void
     */
    protected function registerIronCommand()
    {
        $this->app->bindShared('command.queue.iron', function () {
            return new Commands\QueueIron();
        });

        $this->commands('command.queue.iron');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            'queue',
            'queue.worker',
            'queue.listener',
            'queue.failer',
            'command.queue.work',
            'command.queue.listen',
            'command.queue.subscribe',
            'command.queue.iron',
        ];
    }
}
