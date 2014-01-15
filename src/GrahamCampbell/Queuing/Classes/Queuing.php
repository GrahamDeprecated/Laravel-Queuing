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

use Illuminate\Queue\QueueManager;
use GrahamCampbell\Queuing\Providers\JobProvider;

/**
 * This is the queuing class.
 *
 * @package    Laravel-Queuing
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Queuing/blob/develop/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Queuing
 */
class Queuing
{
    /**
     * The jobs to get pushed.
     *
     * @var array
     */
    protected $jobs = array();

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
    public function __construct(QueueManager $queue, JobProvider $jobprovider, $driver)
    {
        $this->queue = $queue;
        $this->jobprovider = $jobprovider;
        $this->driver = $driver;
    }

    /**
     * Queue the job to memory.
     *
     * @param  mixed   $delay
     * @param  string  $task
     * @param  array   $data
     * @param  string  $queue
     * @return $this
     */
    protected function work($delay, $task, array $data, $queue)
    {
        // check the job
        if ($this->driver === 'sync' && $this->jobprovider->task('cron') === $task) {
            throw new \InvalidArgumentException('A cron job cannot run on the sync queue.');
        }

        // push to memory
        $this->jobs[] = new Job($delay, $task, $data, $queue);

        return $this;
    }

    /**
     * Do the actual job queuing.
     * This method is called on application shutdown.
     *
     * @return $this
     */
    public function process()
    {
        foreach ($this->jobs as $job) {
            $job->push();
        }

        return $this;
    }

    /**
     * Push a new delayed cron job onto the queue.
     *
     * @param  mixed  $delay
     * @param  array  $data
     * @return $this
     */
    public function laterCron($delay, array $data = array())
    {
        return $this->work($delay, $this->jobprovider->task('cron'), $data, $this->jobprovider->queue('cron'));
    }

    /**
     * Push a new cron job onto the queue.
     *
     * @param  array  $data
     * @return $this
     */
    public function pushCron(array $data = array())
    {
        return $this->laterCron(false, $data);
    }

    /**
     * Push a new delayed mail job onto the queue.
     *
     * @param  mixed  $delay
     * @param  array  $data
     * @return $this
     */
    public function laterMail($delay, array $data = array())
    {
        return $this->work($delay, $this->jobprovider->task('mail'), $data, $this->jobprovider->queue('mail'));
    }

    /**
     * Push a new mail job onto the queue.
     *
     * @param  array  $data
     * @return $this
     */
    public function pushMail(array $data = array())
    {
        return $this->laterMail(false, $data);
    }

    /**
     * Push a new delayed job onto the queue.
     *
     * @param  mixed   $delay
     * @param  string  $job
     * @param  string  $queue
     * @param  string  $location
     * @return $this
     */
    public function laterJob($delay, $job, array $data = array(), $location = 'GrahamCampbell\Queuing\Handlers')
    {
        return $this->work($delay, $this->jobprovider->task($job, $location), $data, $this->jobprovider->queue('queue'));
    }

    /**
     * Push a new job onto the queue.
     *
     * @param  string  $job
     * @param  array   $data
     * @return $this
     */
    public function pushJob($job, array $data = array(), $location = 'GrahamCampbell\Queuing\Handlers')
    {
        return $this->laterJob(false, $job, $data, $location);
    }

    /**
     * Clear the specified queue.
     *
     * @param  string  $type
     * @return $this
     */
    protected function clear($type)
    {
        $this->jobprovider->clearQueue($type);
        $queue = $this->jobprovider->queue($type);

        if ($this->driver == 'beanstalkd') {
            $pheanstalk = $this->queue->getPheanstalk();
            try {
                while ($job = $pheanstalk->peekReady($queue)) {
                    $pheanstalk->delete($job);
                }
            } catch (\Pheanstalk_Exception_ServerException $e) {
                // do nothing
            }
            try {
                while ($job = $pheanstalk->peekDelayed($queue)) {
                    $pheanstalk->delete($job);
                }
            } catch (\Pheanstalk_Exception_ServerException $e) {
                // do nothing
            }
            try {
                while ($job = $pheanstalk->peekBuried($queue)) {
                    $pheanstalk->delete($job);
                }
            } catch (\Pheanstalk_Exception_ServerException $e) {
                // do nothing
            }
        } elseif ($this->driver == 'iron') {
            $iron = $this->queue->getIron();
            $iron->clearQueue($queue);
        }

        $this->jobprovider->clearAll();

        return $this;
    }

    /**
     * Clear all cron jobs.
     *
     * @return $this
     */
    public function clearCron()
    {
        return $this->clear('cron')->clear('cron');
    }

    /**
     * Clear all mail jobs.
     *
     * @return $this
     */
    public function clearMail()
    {
        return $this->clear('mail')->clear('mail');
    }

    /**
     * Clear all other jobs.
     *
     * @return $this
     */
    public function clearJobs()
    {
        return $this->clear('jobs')->clear('jobs');
    }

    /**
     * Clear all jobs.
     *
     * @return $this
     */
    public function clearAll()
    {
        $this->clear('cron')->clear('mail')->clear('jobs');
        $this->clear('cron')->clear('mail')->clear('jobs');
        $this->jobprovider->clearAll();

        return $this;
    }

    /**
     * Get the queue length.
     *
     * @return int
     */
    public function length()
    {
        return $this->jobprovider->count();
    }
}
