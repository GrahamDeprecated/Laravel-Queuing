<?php

/*
 * This file is part of Laravel Queuing.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Tests\Queuing;

use GrahamCampbell\Queuing\QueueManager;
use GrahamCampbell\TestBench\AbstractTestCase as AbstractTestBenchTestCase;
use Mockery;
use ReflectionClass;

/**
 * This is the queue manager test class.
 *
 * @author Graham Campbell <graham@mineuk.com>
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

        return [$mock, $mock];
    }
}
