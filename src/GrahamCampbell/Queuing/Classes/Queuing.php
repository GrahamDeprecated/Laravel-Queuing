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

use Illuminate\Queue\QueueManager;
use GrahamCampbell\Queuing\Providers\JobProvider;

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
     * @param  string  $location
     * @return void
     */
    protected function work($delay, $task, array $data, $queue, $location = 'GrahamCampbell\Queuing\Handlers')
    {
        if ($this->driver == 'sync') {
            // check the job
            if ($this->jobprovider->task('cron', $location) == $task) {
                throw new \InvalidArgumentException('A cron job cannot run on the sync queue.');
            }
        } else {
            // push to memory
            $this->jobs[] = new Job($delay, $task, $data, $queue, $location);
        }
    }

    /**
     * Do the actual job queuing.
     * This method is called on application shutdown.
     *
     * @return void
     */
    public function process()
    {
        foreach ($this->jobs as $job) {
            $job->push();
        }
    }

    /**
     * Push a new delayed cron job onto the queue.
     *
     * @param  mixed  $delay
     * @param  array  $data
     * @return void
     */
    public function laterCron($delay, array $data = array())
    {
        $this->work($delay, $this->jobprovider->task('cron'), $data, $this->jobprovider->queue('cron'));
    }

    /**
     * Push a new cron job onto the queue.
     *
     * @param  array  $data
     * @return void
     */
    public function pushCron(array $data = array())
    {
        $this->laterCron(false, $data);
    }

    /**
     * Push a new delayed mail job onto the queue.
     *
     * @param  mixed  $delay
     * @param  array  $data
     * @return void
     */
    public function laterMail($delay, array $data = array())
    {
        $this->work($delay, $this->jobprovider->task('mail'), $data, $this->jobprovider->queue('mail'));
    }

    /**
     * Push a new mail job onto the queue.
     *
     * @param  array  $data
     * @return void
     */
    public function pushMail(array $data = array())
    {
        $this->laterMail(false, $data);
    }

    /**
     * Push a new delayed job onto the queue.
     *
     * @param  mixed   $delay
     * @param  string  $job
     * @param  string  $queue
     * @param  string  $location
     * @return void
     */
    public function laterJob($delay, $job, array $data = array(), $location = 'GrahamCampbell\Queuing\Handlers')
    {
        $this->work($delay, $this->jobprovider->task($job, $location), $data, $this->jobprovider->queue('queue'));
    }

    /**
     * Push a new job onto the queue.
     *
     * @param  string  $job
     * @param  array   $data
     * @return void
     */
    public function pushJob($job, array $data = array())
    {
        $this->laterJob(false, $job, $data);
    }

    /**
     * Clear the specified queue.
     *
     * @param  string  $type
     * @return void
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
    }

    /**
     * Clear all cron jobs.
     *
     * @return void
     */
    public function clearCron()
    {
        $this->clear('cron');
        $this->clear('cron');
    }

    /**
     * Clear all mail jobs.
     *
     * @return void
     */
    public function clearMail()
    {
        $this->clear('mail');
        $this->clear('mail');
    }

    /**
     * Clear all other jobs.
     *
     * @return void
     */
    public function clearJobs()
    {
        $this->clear('jobs');
        $this->clear('jobs');
    }

    /**
     * Clear all jobs.
     *
     * @return void
     */
    public function clearAll()
    {
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
    public function length()
    {
        return $this->jobprovider->count();
    }
}
