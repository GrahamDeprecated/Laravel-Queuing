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

use Illuminate\Queue\IronQueue as LaravelIronQueue;

/**
 * This is the iron queue class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class IronQueue extends LaravelIronQueue implements QueueInterface
{
    use QueueTrait;
}
