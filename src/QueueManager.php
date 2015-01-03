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

use Illuminate\Queue\QueueManager as LaravelQueueManager;

/**
 * This is the queue manager class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class QueueManager extends LaravelQueueManager
{
    /**
     * Process all jobs.
     *
     * @return void
     */
    public function processAll()
    {
        foreach ($this->connections as $connection) {
            $connection->process();
        }
    }
}
