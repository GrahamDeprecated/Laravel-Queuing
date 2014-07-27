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
 * This is the sqs queue test class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Queuing/blob/master/LICENSE.md> Apache 2.0
 */
class SqsQueueTest extends AbstractQueueTestCase
{
    protected function getQueue()
    {
        $mock = Mockery::mock('Aws\Sqs\SqsClient');

        $queue = Mockery::mock(
            'GrahamCampbell\Queuing\Queues\SqsQueue[getQueue]',
            array($mock, 'default')
        )->shouldAllowMockingProtectedMethods();

        $queue->shouldReceive('getQueue')->twice()->with('')->andReturn('default');

        $mock->shouldReceive('sendMessage')->once()
            ->with(array('QueueUrl' => 'default', 'MessageBody' => '{"job":"foo","data":["foodata"]}'))
            ->andReturn(new SqsStub);

        $mock->shouldReceive('sendMessage')->once()
            ->with(array('QueueUrl' => 'default', 'MessageBody' => '{"job":"bar","data":["bardata"]}', 'DelaySeconds' => 666))
            ->andReturn(new SqsStub);

        return $queue;
    }
}

class SqsStub
{
    public function get($name)
    {
        return 666;
    }
}
