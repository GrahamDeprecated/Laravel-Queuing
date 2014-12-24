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
 * This is the beanstalkd queue test class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Queuing/blob/master/LICENSE.md> Apache 2.0
 */
class BeanstalkdQueueTest extends AbstractQueueTestCase
{
    protected function getQueue()
    {
        $mock = Mockery::mock('Pheanstalk_Pheanstalk');

        $queue = Mockery::mock(
            'GrahamCampbell\Queuing\Queues\BeanstalkdQueue[getQueue]',
            [$mock, 'default', 10]
        )->shouldAllowMockingProtectedMethods();

        $queue->shouldReceive('getQueue')->twice()->with('')->andReturn('default');

        $mock->shouldReceive('useTube')->twice()->with('default')->andReturnSelf();

        $mock->shouldReceive('put')->once()->with('{"job":"foo","data":["foodata"]}', 1024, 0, 10);
        $mock->shouldReceive('put')->once()->with('{"job":"bar","data":["bardata"]}', 1024, 666, 10);

        return $queue;
    }
}
