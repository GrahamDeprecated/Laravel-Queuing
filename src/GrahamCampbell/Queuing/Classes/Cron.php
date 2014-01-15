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

namespace GrahamCampbell\Queuing\Classes;

use Closure;
use Illuminate\Support\Facades\Event;

/**
 * This is the cron class.
 *
 * @package    Laravel-Queuing
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Queuing/blob/develop/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Queuing
 */
class Cron
{
    /**
     * The cron tasks.
     *
     * @var array
     */
    protected $tasks = array();

    /**
     * The queuing instance.
     *
     * @var \GrahamCampbell\Queuing\Classes\Queuing
     */
    protected $queuing;

    /**
     * Create a new instance.
     *
     * @param  \GrahamCampbell\Queuing\Classes\Queuing  $queuing
     * @return void
     */
    public function __construct(Queuing $queuing)
    {
        $this->queuing = $queuing;
    }

    /**
     * Start the cron jobs after a delay.
     *
     * @param  \Carbon\Carbon|int  $delay
     * @return $this
     */
    public function start($delay = 1000)
    {
        $this->stop();
        Event::fire('cron.starting');
        $this->queuing->laterCron($delay, array('tasks' => $this->tasks));

        return $this;
    }

    /**
     * Stop the cron jobs.
     *
     * @return $this
     */
    public function stop()
    {
        Event::fire('cron.stopping');
        $this->queuing->clearCron();

        return $this;
    }

    /**
     * Add a task closure to the cron.
     * This should be called after listening for a cron.starting event.
     *
     * @param  \Closure  $task
     * @return $this
     */
    public function add(Closure $task)
    {
        $this->tasks[] = $task;

        return $this;
    }
}
