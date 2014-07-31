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

namespace GrahamCampbell\Queuing\Queues;

/**
 * This is the queue trait.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Queuing/blob/master/LICENSE.md> Apache 2.0
 */
trait QueueTrait
{
    /**
     * The jobs to get pushed.
     *
     * @var array
     */
    protected $jobs = array();

    /**
     * Push a raw payload onto the queue.
     *
     * @param string $payload
     * @param string $queue
     * @param array  $options
     *
     * @return void
     */
    public function pushRaw($payload, $queue = null, array $options = array())
    {
        $this->jobs[] = array(
            'type'    => 'push',
            'payload' => $payload,
            'queue'   => $queue,
            'options' => $options
        );
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param \DateTime|int $delay
     * @param string        $job
     * @param mixed         $data
     * @param string        $queue
     *
     * @return void
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        $this->jobs[] = array(
            'type'  => 'later',
            'delay' => $delay,
            'job'   => $job,
            'data'  => $data,
            'queue' => $queue
        );
    }

    /**
     * Process all jobs in the queue.
     *
     * @return void
     */
    public function process()
    {
        foreach ($this->jobs as $id => $job) {
            // get the job name
            $name = ucfirst($job['type']);
            // process the job
            $this->{"process{$name}"}($job);
            // remove it from the processing queue
            unset($this->jobs[$id]);
        }
    }

    /**
     * Process a "push".
     *
     * @param array $job
     *
     * @return void
     */
    protected function processPush($job)
    {
        parent::pushRaw($job['payload'], $job['queue'], $job['options']);
    }

    /**
     * Process a "later".
     *
     * @param array $job
     *
     * @return void
     */
    protected function processLater($job)
    {
        parent::later($job['delay'], $job['job'], $job['data'], $job['queue']);
    }
}
