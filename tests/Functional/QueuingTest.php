<?php

/**
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

namespace GrahamCampbell\Tests\Queuing\Functional;

use GrahamCampbell\Tests\Queuing\AbstractTestCase;
use Illuminate\Support\Facades\Queue;

/**
 * This is the queuing test class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Queuing/blob/master/LICENSE.md> Apache 2.0
 */
class QueuingTest extends AbstractTestCase
{
    public function connectionProvider()
    {
        return array(
            array('sync', 'GrahamCampbell\Queuing\Queues\SyncQueue'),
            array('beanstalkd', 'GrahamCampbell\Queuing\Queues\BeanstalkdQueue'),
            array('redis', 'GrahamCampbell\Queuing\Queues\RedisQueue'),
            array('sqs', 'GrahamCampbell\Queuing\Queues\SqsQueue'),
            array('iron', 'GrahamCampbell\Queuing\Queues\IronQueue'),
        );
    }

    /**
     * @dataProvider connectionProvider
     */
    public function testSetup($name, $class)
    {
        $this->assertInstanceOf($class, Queue::connection($name));
    }
}
