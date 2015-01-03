<?php

/*
 * This file is part of Laravel Queuing.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Tests\Queuing\Queues;

use Mockery;

/**
 * This is the redis queue test class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class RedisQueueTest extends AbstractQueueTestCase
{
    protected function getQueue()
    {
        $mock = Mockery::mock('Illuminate\Redis\Database');

        $queue = Mockery::mock(
            'GrahamCampbell\Queuing\Queues\RedisQueue[getQueue,getRandomId,getTime]',
            [$mock, 'default', null]
        )->shouldAllowMockingProtectedMethods();

        $queue->shouldReceive('getQueue')->twice()->with('')->andReturn('default');

        $queue->shouldReceive('getRandomId')->twice()->andReturn('notsorandomid');

        $queue->shouldReceive('getTime')->once()->andReturn(568);

        $client = Mockery::mock('Predis\Client');

        $mock->shouldReceive('connection')->twice()->with('')->andReturn($client);

        $client->shouldReceive('rpush')->once()
            ->with('default', '{"job":"foo","data":["foodata"],"id":"notsorandomid","attempts":1}');

        $client->shouldReceive('zadd')->once()
            ->with('default:delayed', 1234, '{"job":"bar","data":["bardata"],"id":"notsorandomid","attempts":1}');

        return $queue;
    }
}
