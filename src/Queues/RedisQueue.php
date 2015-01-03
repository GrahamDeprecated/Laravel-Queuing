<?php

/*
 * This file is part of Laravel Queuing.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Queuing\Queues;

use Illuminate\Queue\RedisQueue as LaravelRedisQueue;

/**
 * This is the redis queue class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class RedisQueue extends LaravelRedisQueue implements QueueInterface
{
    use QueueTrait;
}
