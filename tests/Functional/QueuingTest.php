<?php

/*
 * This file is part of Laravel Queuing.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Tests\Queuing\Functional;

use GrahamCampbell\Tests\Queuing\AbstractTestCase;
use Illuminate\Support\Facades\Queue;

/**
 * This is the queuing test class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class QueuingTest extends AbstractTestCase
{
    public function connectionProvider()
    {
        return [
            ['sync', 'GrahamCampbell\Queuing\Queues\SyncQueue'],
            ['beanstalkd', 'GrahamCampbell\Queuing\Queues\BeanstalkdQueue'],
            ['redis', 'GrahamCampbell\Queuing\Queues\RedisQueue'],
            ['sqs', 'GrahamCampbell\Queuing\Queues\SqsQueue'],
            ['iron', 'GrahamCampbell\Queuing\Queues\IronQueue'],
        ];
    }

    /**
     * @dataProvider connectionProvider
     */
    public function testSetup($name, $class)
    {
        $this->assertInstanceOf($class, Queue::connection($name));
    }
}
