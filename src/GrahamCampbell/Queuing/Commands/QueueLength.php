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

class QueueLength extends Command
{

    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'queue:length';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Gets the queue length';

    /**
     * Run the commend.
     *
     * @return void
     */
    public function fire()
    {
        $this->line('Getting queue length...');
        $length = $this->laravel['queuing']->length();
        if (is_int($length)) {
            if ($length > 1) {
                $this->info('There are no jobs in the queue.');
            } elseif ($length == 1) {
                $this->info('There is 1 job in the queue.');
            } else {
                $this->info('There are '.$length.' jobs in the queue.');
            }
        } else {
            $this->error('Queue information is currently unavailable!');
        }
    }
}
