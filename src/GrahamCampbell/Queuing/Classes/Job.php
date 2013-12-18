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

use Carbon\Carbon;
use GrahamCampbell\Queuing\Facades\JobProvider;

/**
 * This is the job class.
 *
 * @package    Laravel-Queuing
 * @author     Graham Campbell
 * @copyright  Copyright 2013 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Queuing/blob/develop/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Queuing
 */
class Job
{
    /**
     * The minimum delay for a delayed queue push.
     *
     * @var int
     */
    protected $min = 5;

    /**
     * The job delay.
     *
     * @var int
     */
    protected $delay;

    /**
     * The job task.
     *
     * @var string
     */
    protected $task;

    /**
     * The job data.
     *
     * @var array
     */
    protected $data;

    /**
     * The job queue.
     *
     * @var string
     */
    protected $queue;

    /**
     * The job location.
     *
     * @var string
     */
    protected $location;

    /**
     * Create a new instance.
     *
     * @param  mixed   $delay
     * @param  string  $task
     * @param  array   $data
     * @param  string  $queue
     * @param  string  $location
     * @return void
     */
    public function __construct($delay, $task, array $data, $queue, $location = 'GrahamCampbell\Queuing\Handlers')
    {
        $this->delay = $delay;
        $this->task = $task;
        $this->data = $data;
        $this->queue = $queue;
        $this->location = $location;
    }

    /**
     * Do the actual job queuing.
     *
     * @return void
     */
    public function push()
    {
        // push to the database server
        $model = JobProvider::create(array('task' => $this->task, 'queue' => $this->queue));
        // save model id
        $this->data['model_id'] = $model->getId();

        // push to the queuing server
        if ($this->delay === false) {
            Queue::push($this->task, $this->data, $this->queue);
        } else {
            Queue::later($this->time($this->delay), $this->task, $this->data, $this->queue);
        }
    }

    /**
     * Convert to a valid time.
     *
     * @param  mixed  $time
     * @return int
     */
    protected function time($time = null)
    {
        if (is_object($time)) {
            if (get_class($time) == 'Carbon\Carbon') {
                return $this->times(Carbon::now()->diffInSeconds($time));
            }
        }

        if (is_int($time)) {
            return $this->times($time);
        }

        return $this->times();
    }

    /**
     * Convert to a valid time strictly.
     *
     * @param  mixed  $time
     * @return int
     */
    protected function times($time = null)
    {
        if (is_int($time)) {
            if ($time >= $this->min) {
                return $time;
            }
        }

        return $this->min;
    }
}
