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
 * This is the iron queue test class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class IronQueueTest extends AbstractQueueTestCase
{
    protected function getQueue()
    {
        $mock = Mockery::mock('IronMQ');
        $request = Mockery::mock('Illuminate\Http\Request');

        $queue = Mockery::mock(
            'GrahamCampbell\Queuing\Queues\IronQueue[getQueue]',
            [$mock, $request, 'default']
        )->shouldAllowMockingProtectedMethods();

        $queue->shouldReceive('getQueue')->times(4)->with('')->andReturn('default');

        $mock->shouldReceive('postMessage')->once()
            ->with('default', '{"job":"foo","data":["foodata"],"attempts":1,"queue":"default"}', [])
            ->andReturn((object) ['id' => 666]);

        $mock->shouldReceive('postMessage')->once()
            ->with('default', '{"job":"bar","data":["bardata"],"attempts":1,"queue":"default"}', ['delay' => 666])
            ->andReturn((object) ['id' => 666]);

        return $queue;
    }
}
