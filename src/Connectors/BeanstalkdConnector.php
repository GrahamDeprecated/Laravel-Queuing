<?php

/*
 * This file is part of Laravel Queuing.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Queuing\Connectors;

use GrahamCampbell\Queuing\Queues\BeanstalkdQueue;
use Illuminate\Queue\Connectors\BeanstalkdConnector as LaravelBeanstalkdConnector;
use Pheanstalk_Pheanstalk as Pheanstalk;

/**
 * This is the beanstalkd queue connector class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class BeanstalkdConnector extends LaravelBeanstalkdConnector
{
    /**
     * Establish a queue connection.
     *
     * @param array $config
     *
     * @return \GrahamCampbell\Queuing\Queues\BeanstalkdQueue
     */
    public function connect(array $config)
    {
        $pheanstalk = new Pheanstalk($config['host'], array_get($config, 'port', Pheanstalk::DEFAULT_PORT));
        $ttr = array_get($config, 'ttr', Pheanstalk::DEFAULT_TTR);

        return new BeanstalkdQueue($pheanstalk, $config['queue'], $ttr);
    }
}
