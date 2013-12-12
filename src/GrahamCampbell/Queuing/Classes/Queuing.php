<?php namespace GrahamCampbell\Queuing\Classes;

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

use Carbon\Carbon;
use Illuminate\Queue\QueueManager;
use GrahamCampbell\Queuing\Providers\JobProvider;

class Queuing {

    /**
     * The minimum delay for a delayed queue push.
     *
     * @var int
     */
    protected $delay = 5;

    /**
     * The queue instance.
     *
     * @var \Illuminate\Queue\QueueManager
     */
    protected $queue;

    /**
     * The jobprovider instance.
     *
     * @var \GrahamCampbell\Queuing\Providers\JobProvider
     */
    protected $jobprovider;

    /**
     * The queue driver.
     *
     * @var string
     */
    protected $driver;

    /**
     * Create a new instance.
     *
     * @param  \Illuminate\Queue\QueueManager  $queue
     * @param  \GrahamCampbell\Queuing\Providers\JobProvider  $jobprovider
     * @param  string  $driver
     * @return void
     */
    public function __construct(QueueManager $queue, JobProvider $jobprovider, $driver) {
        $this->queue = $queue;
        $this->jobprovider = $jobprovider;
        $this->driver = $driver;
    }

    /**
     * Convert to a valid time.
     *
     * @param  mixed  $time
     * @return int
     */
    protected function time($time = null) {
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
    protected function times($time = null) {
        if (is_int($time)) {
            if ($time >= $this->delay) {
                return $time;
            }
        }

        return $this->delay;
    }

    /**
     * Do the actual job queuing.
     *
     * @param  mixed   $delay
     * @param  string  $task
     * @param  mixed   $data
     * @param  string  $queue
     * @param  string  $location
     * @return \GrahamCampbell\Queuing\Models\Job
     */
    protected function work($delay, $task, $data, $queue, $location = 'GrahamCampbell\Queuing\Handlers') {
        if ($this->driver == 'sync') {
            // check the job
            if ($this->jobprovider->task('cron', $location) == $task) {
                throw new \InvalidArgumentException('A cron job cannot run on the sync queue.');
            }
        } else {
            // push to the database server
            $model = $this->jobprovider->create(array('task' => $task, 'queue' => $queue));
            // save model id
            $data['model_id'] = $model->getId();
        }

        // push to the queuing server
        if ($delay === false) {
            $this->queue->push($task, $data, $queue);
        } else {
            $time = $this->time($delay);
            $this->queue->later($time, $task, $data, $queue);
        }

        // return the model
        if (isset($model)) {
            return $model;
        }
    }

    /**
     * Push a new delayed cron job onto the queue.
     *
     * @param  mixed  $delay
     * @param  array  $data
     * @return \GrahamCampbell\Queuing\Models\Job
     */
    public function laterCron($delay, array $data = array()) {
        return $this->work($delay, $this->jobprovider->task('cron'), $data, $this->jobprovider->queue('cron'));
    }

    /**
     * Push a new cron job onto the queue.
     *
     * @param  array  $data
     * @return \GrahamCampbell\Queuing\Models\Job
     */
    public function pushCron(array $data = array()) {
        return $this->laterCron(false, $data);
    }

    /**
     * Push a new delayed mail job onto the queue.
     *
     * @param  mixed  $delay
     * @param  array  $data
     * @return \GrahamCampbell\Queuing\Models\Job
     */
    public function laterMail($delay, array $data = array()) {
        return $this->work($delay, $this->jobprovider->task('mail'), $data, $this->jobprovider->queue('mail'));
    }

    /**
     * Push a new mail job onto the queue.
     *
     * @param  array  $data
     * @return \GrahamCampbell\Queuing\Models\Job
     */
    public function pushMail(array $data = array()) {
        return $this->laterMail(false, $data);
    }

    /**
     * Push a new delayed job onto the queue.
     *
     * @param  mixed   $delay
     * @param  string  $job
     * @param  string  $queue
     * @param  string  $location
     * @return \GrahamCampbell\Queuing\Models\Job
     */
    public function laterJob($delay, $job, array $data = array(), $location = 'GrahamCampbell\Queuing\Handlers') {
        return $this->work($delay, $this->jobprovider->task($job, $location), $data, $this->jobprovider->queue('queue'));
    }

    /**
     * Push a new job onto the queue.
     *
     * @param  string  $job
     * @param  array   $data
     * @return \GrahamCampbell\Queuing\Models\Job
     */
    public function pushJob($job, array $data = array()) {
        return $this->laterJob(false, $job, $data);
    }

    /**
     * Clear the specified queue.
     *
     * @param  string  $type
     * @return void
     */
    protected function clear($type) {
        $this->jobprovider->clearQueue($type);
        $queue = $this->jobprovider->queue($type);

        if ($this->driver == 'beanstalkd') {
            $pheanstalk = $this->queue->getPheanstalk();
            try {
                while($job = $pheanstalk->peekReady($queue)) {
                    $pheanstalk->delete($job);
                }
            } catch (\Pheanstalk_Exception_ServerException $e) {}
            try {
                while($job = $pheanstalk->peekDelayed($queue)) {
                    $pheanstalk->delete($job);
                }
            } catch (\Pheanstalk_Exception_ServerException $e) {}
            try {
                while($job = $pheanstalk->peekBuried($queue)) {
                    $pheanstalk->delete($job);
                }
            } catch (\Pheanstalk_Exception_ServerException $e) {}
        } elseif ($this->driver == 'iron') {
            $iron = $this->queue->getIron();
            $iron->clearQueue($queue);
        }

        $this->jobprovider->clearAll();
    }

    /**
     * Clear all cron jobs.
     *
     * @return void
     */
    public function clearCron() {
        $this->clear('cron');
        $this->clear('cron');
    }

    /**
     * Clear all mail jobs.
     *
     * @return void
     */
    public function clearMail() {
        $this->clear('mail');
        $this->clear('mail');
    }

    /**
     * Clear all other jobs.
     *
     * @return void
     */
    public function clearJobs() {
        $this->clear('jobs');
        $this->clear('jobs');
    }

    /**
     * Clear all jobs.
     *
     * @return void
     */
    public function clearAll() {
        $this->clear('cron');
        $this->clear('mail');
        $this->clear('jobs');
        $this->clear('cron');
        $this->clear('mail');
        $this->clear('jobs');
        $this->jobprovider->clearAll();
    }

    /**
     * Get the queue length.
     *
     * @return int
     */
    public function length() {
        return $this->jobprovider->count();
    }
}
