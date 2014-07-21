<?php

/**
 * This file is part of Laravel Queuing by Graham Campbell.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
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
 * This is the iron queue test class.
 *
 * @package    Laravel-Queuing
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Queuing/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Queuing
 */
class IronQueueTest extends AbstractQueueTestCase
{
    protected function getQueue()
    {
        $mock = Mockery::mock('IronMQ');
        $request = Mockery::mock('Illuminate\Http\Request');

        $queue = Mockery::mock(
            'GrahamCampbell\Queuing\Queues\IronQueue[getQueue]',
            array($mock, $request, 'default')
        )->shouldAllowMockingProtectedMethods();

        $queue->shouldReceive('getQueue')->times(4)->with('')->andReturn('default');
        $queue->shouldReceive('getQueue')->once()->with('default')->andReturn('default');

        $mock->shouldReceive('postMessage')->once()
            ->with('default', '{"job":"foo","data":["foodata"],"attempts":1,"queue":"default"}', array())
            ->andReturn((object) array('id' => 666));

        $mock->shouldReceive('postMessage')->once()
            ->with('default', '{"job":"bar","data":["bardata"],"attempts":1,"queue":"default"}', array('delay' => 666))
            ->andReturn((object) array('id' => 666));

        return $queue;
    }
}
