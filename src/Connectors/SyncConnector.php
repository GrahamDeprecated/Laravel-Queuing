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

use GrahamCampbell\Queuing\Queues\SyncQueue;
use Illuminate\Queue\Connectors\SyncConnector as LaravelSyncConnector;

/**
 * This is the sync queue connector class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class SyncConnector extends LaravelSyncConnector
{
    /**
     * Establish a queue connection.
     *
     * @param array $config
     *
     * @return \GrahamCampbell\Queuing\Queues\SyncQueue
     */
    public function connect(array $config)
    {
        return new SyncQueue();
    }
}
