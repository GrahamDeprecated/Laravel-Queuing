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
 * This is the cycle test class.
 *
 * @author Graham Campbell <graham@mineuk.com>
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
