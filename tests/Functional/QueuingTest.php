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

namespace GrahamCampbell\Tests\Queuing\Functional;

use Illuminate\Support\Facades\Queue;
use GrahamCampbell\Tests\Queuing\AbstractTestCase;

/**
 * This is the queuing test class.
 *
 * @package    Laravel-Queuing
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Queuing/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Queuing
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
            array('iron', 'GrahamCampbell\Queuing\Queues\IronQueue')
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
