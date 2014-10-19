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

namespace GrahamCampbell\Queuing\Commands;

use Illuminate\Console\Command;

/**
 * This is the queue iron command class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Queuing/blob/master/LICENSE.md> Apache 2.0
 */
class QueueIron extends Command
{
    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'queue:iron';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Sets up IronMQ subscriptions';

    /**
     * Run the commend.
     *
     * @return void
     */
    public function fire()
    {
        $this->line('Setting up iron queueing...');

        try {
            $this->call('queue:subscribe', array(
                'queue' => $this->laravel['config']['queue.connections.iron.queue'],
                'url' => $this->laravel['url']->to('queue/receive'),
            ));
            $this->info('Queueing is now setup!');
        } catch (\Exception $e) {
            $this->error('Iron queuing could not be setup!');
        }
    }
}
