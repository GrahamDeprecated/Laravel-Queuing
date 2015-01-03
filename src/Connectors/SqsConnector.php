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

use Aws\Sqs\SqsClient;
use GrahamCampbell\Queuing\Queues\SqsQueue;
use Illuminate\Queue\Connectors\SqsConnector as LaravelSqsConnector;

/**
 * This is the sqs queue connector class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class SqsConnector extends LaravelSqsConnector
{
    /**
     * Establish a queue connection.
     *
     * @param array $config
     *
     * @return \GrahamCampbell\Queuing\Queues\SqsQueue
     */
    public function connect(array $config)
    {
        $sqs = SqsClient::factory($config);

        return new SqsQueue($sqs, $config['queue']);
    }
}
