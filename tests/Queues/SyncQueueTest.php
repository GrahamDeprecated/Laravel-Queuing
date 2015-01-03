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
 * This is the sync queue test class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class SyncQueueTest extends AbstractQueueTestCase
{
    public function testPushAndProcess()
    {
        $queue = $this->getQueue();

        $this->assertNull($queue->push('foo', ['foodata']));
        $this->assertNull($queue->push('bar', ['bardata']));

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
