<?php

/*
 * This file is part of Laravel Queuing by Graham Campbell.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at http://bit.ly/UWsjkb.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace GrahamCampbell\Tests\Queuing\Queues;

use Mockery;

/**
 * This is the redis queue test class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Queuing/blob/master/LICENSE.md> Apache 2.0
 */
class RedisQueueTest extends AbstractQueueTestCase
{
    protected function getQueue()
    {
        $mock = Mockery::mock('Illuminate\Redis\Database');

        $queue = Mockery::mock(
            'GrahamCampbell\Queuing\Queues\RedisQueue[getQueue,getRandomId,getTime]',
            array($mock, 'default', null)
        )->shouldAllowMockingProtectedMethods();

        $queue->shouldReceive('getQueue')->twice()->with('')->andReturn('default');

        $queue->shouldReceive('getRandomId')->twice()->andReturn('notsorandomid');

        $queue->shouldReceive('getTime')->once()->andReturn(568);

        $mock->shouldReceive('rpush')->once()
            ->with('default', '{"job":"foo","data":["foodata"],"id":"notsorandomid","attempts":1}');

        $mock->shouldReceive('zadd')->once()
            ->with('default:delayed', 1234, '{"job":"bar","data":["bardata"],"id":"notsorandomid","attempts":1}');

        return $queue;
    }
}
