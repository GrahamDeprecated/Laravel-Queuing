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
 * This is the beanstalkd queue test class.
 *
 * @author Graham Campbell <graham@mineuk.com>
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
