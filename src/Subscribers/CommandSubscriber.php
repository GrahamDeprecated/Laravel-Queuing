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

namespace GrahamCampbell\Queuing\Subscribers;

use Illuminate\Support\Facades\Config;

/**
 * This is the command subscriber class.
 *
 * @package    Laravel-Queuing
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Queuing/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Queuing
 */
class CommandSubscriber
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        $events->listen('command.runmigrations', 'GrahamCampbell\Core\Subscribers\CommandSubscriber@onRunMigrations', 2);
        $events->listen('command.extrastuff', 'GrahamCampbell\Core\Subscribers\CommandSubscriber@onExtraStuff', 8);
    }

    /**
     * Handle a command.runmigrations event.
     *
     * @param  GrahamCampbell\Core\Commands\AbstractCommand  $command
     * @return void
     */
    public function onRunMigrations($command)
    {
        $command->call('migrate', array('--package' => 'graham-campbell/queuing'));
    }

    /**
     * Handle a command.extrastuff event.
     *
     * @param  GrahamCampbell\Core\Commands\AbstractCommand  $command
     * @return void
     */
    public function onExtraStuff($command)
    {
        if (Config::get('queue.default') == 'sync') {
            $command->comment('Please note that cron functionality is disabled.');
        } else {
            $command->call('cron:start');
        }
    }
}
