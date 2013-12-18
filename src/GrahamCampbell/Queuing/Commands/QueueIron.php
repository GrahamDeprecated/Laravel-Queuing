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

namespace GrahamCampbell\Queuing\Commands;

use Illuminate\Console\Command;

/**
 * This is the queue iron command class.
 *
 * @package    Laravel-Queuing
 * @author     Graham Campbell
 * @copyright  Copyright 2013 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Queuing/blob/develop/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Queuing
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
    protected $description = 'Setups up IronMQ subscriptions';

    /**
     * Run the commend.
     *
     * @return void
     */
    public function fire()
    {
        $this->line('Setting up iron queueing...');

        if ($this->laravel['queue']['queue.default'] !== 'iron') {
            $this->error('The current config is not setup for iron queueing!');
        }

        $this->call('queue:subscribe', array('queue' => $this->laravel['jobprovider']->queue('queue'), 'url' => URL::route('queuing.index')));

        $this->call('queue:subscribe', array('queue' => $this->laravel['jobprovider']->queue('mail'), 'url' => URL::route('queuing.index')));

        $this->call('queue:subscribe', array('queue' => $this->laravel['jobprovider']->queue('cron'), 'url' => URL::route('queuing.index')));

        $this->info('Queueing is now setup!');
    }
}
