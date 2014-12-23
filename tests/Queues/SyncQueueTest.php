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
 * This is the sync queue test class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Queuing/blob/master/LICENSE.md> Apache 2.0
 */
class SyncQueueTest extends AbstractQueueTestCase
{
    public function testPushAndProcess()
    {
        $queue = $this->getQueue();

        $this->assertNull($queue->push('foo', array('foodata')));
        $this->assertNull($queue->push('bar', array('bardata')));

        // process once - jobs are processed and unset
        $queue->process();

        // process again - nothing should happen
        $queue->process();
    }

    protected function getQueue()
    {
        $queue = Mockery::mock('GrahamCampbell\Queuing\Queues\SyncQueue[resolveJob]')
            ->shouldAllowMockingProtectedMethods();

        $job = Mockery::mock('Illuminate\Queue\Jobs\SyncJob');
        $job->shouldReceive('fire')->twice();

        $queue->shouldReceive('resolveJob')->once()
            ->with('foo', '["foodata"]')->andReturn($job);
        $queue->shouldReceive('resolveJob')->once()
            ->with('bar', '["bardata"]')->andReturn($job);

        return $queue;
    }
}
