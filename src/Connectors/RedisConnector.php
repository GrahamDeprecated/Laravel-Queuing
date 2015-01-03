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

use GrahamCampbell\Queuing\Queues\RedisQueue;
use Illuminate\Queue\Connectors\RedisConnector as LaravelRedisConnector;

/**
 * This is the redis queue connector class.
 *
 * @author Graham Campbell <graham@mineuk.com>
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
