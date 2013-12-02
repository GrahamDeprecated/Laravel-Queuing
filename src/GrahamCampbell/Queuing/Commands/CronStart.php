<?php namespace GrahamCampbell\Queuing\Commands;

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
 *
 * @package    Laravel-Queuing
 * @author     Graham Campbell
 * @license    Apache License
 * @copyright  Copyright 2013 Graham Campbell
 * @link       https://github.com/GrahamCampbell/Laravel-Queuing
 */

use Illuminate\Console\Command;

class CronStart extends Command {

    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'cron:start';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Starts the cron jobs';

    /**
     * Run the commend.
     *
     * @return void
     */
    public function fire() {
        $this->line('Starting cron...');
        if ($this->laravel['config']['queue.default'] == 'sync') {
            $this->error('Cron cannot run on the sync queue.');
            $this->comment('Please change the queue in the config.');
        } else {
            $this->laravel['cron']->start(30);
            $this->info('Cron started!');
        }
    }
}
