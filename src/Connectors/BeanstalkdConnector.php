<?php

/**
 * This file is part of Laravel Queuing by Graham Campbell.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at http://bit.ly/UWsjkb.
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
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Queuing/blob/master/LICENSE.md> Apache 2.0
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