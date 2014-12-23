<?php

/*
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

use GrahamCampbell\Queuing\Queues\RedisQueue;
use Illuminate\Queue\Connectors\RedisConnector as LaravelRedisConnector;

/**
 * This is the redis queue connector class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Queuing/blob/master/LICENSE.md> Apache 2.0
 */
class RedisConnector extends LaravelRedisConnector
{
    /**
     * Establish a queue connection.
     *
     * @param array $config
     *
     * @return \GrahamCampbell\Queuing\Queues\RedisQueue
     */
    public function connect(array $config)
    {
        return new RedisQueue($this->redis, $config['queue'], $this->connection);
    }
}
