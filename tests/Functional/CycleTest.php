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
 * This is the cycle test class.
 *
 * @package    Laravel-Queuing
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Queuing/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Queuing
 */
class CycleTest extends AbstractTestCase
{
    public $foo = false;

    /**
     * Specify if routing filters are enabled.
     *
     * @return bool
     */
    protected function enableFilters()
    {
        return true;
    }

    public function testRequest()
    {
        $me = $this;
        $this->app['router']->get('queuing-test-route', function () use ($me) {
            Queue::push(function () use ($me) {
                $me->foo = true;
            });
        });

        $this->call('GET', 'queuing-test-route');

        $this->assertTrue($this->foo);
    }

    /**
     * @expectedException \Illuminate\Encryption\DecryptException
     */
    public function testIron()
    {
        $this->call('POST', 'queue/receive');
    }
}
