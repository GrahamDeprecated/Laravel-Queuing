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

namespace GrahamCampbell\Tests\Queuing;

use GrahamCampbell\Queuing\QueueManager;
use GrahamCampbell\TestBench\AbstractTestCase as AbstractTestBenchTestCase;
use Mockery;
use ReflectionClass;

/**
 * This is the queue manager test class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Queuing/blob/master/LICENSE.md> Apache 2.0
 */
class QueueManagerTest extends AbstractTestBenchTestCase
{
    public function testProcessAll()
    {
        $manager = $this->getManager();

        $reflection = new ReflectionClass('GrahamCampbell\Queuing\QueueManager');
        $property = $reflection->getProperty('connections');
        $property->setAccessible(true);

        $property->setValue($manager, $this->getConnections());

        $this->assertNull($manager->processAll());
    }

    protected function getManager()
    {
        $app = Mockery::mock('Illuminate\Foundation\Application');

        return new QueueManager($app);
    }

    protected function getConnections()
    {
        $mock = Mockery::mock('GrahamCampbell\Queuing\Queues\QueueInterface');

        $mock->shouldReceive('process')->twice();

        return array($mock, $mock);
    }
}
