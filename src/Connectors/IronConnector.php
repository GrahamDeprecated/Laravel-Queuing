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

use GrahamCampbell\Queuing\Queues\IronQueue;
use Illuminate\Queue\Connectors\IronConnector as LaravelIronConnector;
use IronMQ;

/**
 * This is the iron queue connector class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class IronConnector extends LaravelIronConnector
{
    /**
     * Establish a queue connection.
     *
     * @param array $config
     *
     * @return \GrahamCampbell\Queuing\Queues\IronQueue
     */
    public function connect(array $config)
    {
        $ironConfig = ['token' => $config['token'], 'project_id' => $config['project']];

        if (isset($config['host'])) {
            $ironConfig['host'] = $config['host'];
        }

        $iron = new IronMQ($ironConfig);

        if (isset($config['ssl_verifypeer'])) {
            $iron->ssl_verifypeer = $config['ssl_verifypeer'];
        }

        return new IronQueue($iron, $this->request, $config['queue'], $config['encrypt']);
    }
}
