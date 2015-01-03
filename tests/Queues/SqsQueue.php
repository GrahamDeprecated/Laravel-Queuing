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
 * This is the sqs queue test class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class SqsQueueTest extends AbstractQueueTestCase
{
    protected function getQueue()
    {
        $mock = Mockery::mock('Aws\Sqs\SqsClient');

        $queue = Mockery::mock(
            'GrahamCampbell\Queuing\Queues\SqsQueue[getQueue]',
            [$mock, 'default']
        )->shouldAllowMockingProtectedMethods();

        $queue->shouldReceive('getQueue')->twice()->with('')->andReturn('default');

        $mock->shouldReceive('sendMessage')->once()
            ->with(['QueueUrl' => 'default', 'MessageBody' => '{"job":"foo","data":["foodata"]}'])
            ->andReturn(new SqsStub());

        $mock->shouldReceive('sendMessage')->once()
            ->with(['QueueUrl' => 'default', 'MessageBody' => '{"job":"bar","data":["bardata"]}', 'DelaySeconds' => 666])
            ->andReturn(new SqsStub());

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
