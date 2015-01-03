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

use GrahamCampbell\TestBench\AbstractTestCase;

/**
 * This is the abstract queue test case class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
abstract class AbstractQueueTestCase extends AbstractTestCase
{
    public function testPushAndProcess()
    {
        $queue = $this->getQueue();

        $this->assertNull($queue->push('foo', ['foodata']));
        $this->assertNull($queue->later(666, 'bar', ['bardata']));

        // process once - jobs are processed and unset
        $queue->process();

        // process again - nothing should happen
        $queue->process();
    }

    abstract protected function getQueue();
}
