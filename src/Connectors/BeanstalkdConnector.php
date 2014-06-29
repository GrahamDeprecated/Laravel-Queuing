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

namespace GrahamCampbell\Queuing\Connectors;

use GrahamCampbell\Queuing\Queues\BeanstalkdQueue;
use Illuminate\Queue\Connectors\BeanstalkdConnector as LaravelBeanstalkdConnector;

/**
 * This is the beanstalkd queue connector class.
 *
 * @package    Laravel-Queuing
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Queuing/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Queuing
 */
class BeanstalkdConnector extends LaravelBeanstalkdConnector
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \Illuminate\Queue\QueueInterface
     */
    public function connect(array $config)
    {
        // laravel 4.2/4.3 compatability hack
        if (class_exists('\Pheanstalk_Pheanstalk')) {
            $class = '\Pheanstalk_Pheanstalk';
        } else {
            $class = '\Pheanstalk\Pheanstalk';
        }

        $pheanstalk = new $class($config['host']);

        return new BeanstalkdQueue(
            $pheanstalk, $config['queue'], array_get($config, 'ttr', $class::DEFAULT_TTR)
        );
    }
}
