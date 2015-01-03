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

use Illuminate\Queue\QueueInterface as LaravelQueueInterface;

/**
 * This is the queue interface.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
interface QueueInterface extends LaravelQueueInterface
{
    /**
     * Process all jobs in the queue.
     *
     * @return void
     */
    public function process();
}
