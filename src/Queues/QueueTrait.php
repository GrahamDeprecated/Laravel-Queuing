<?php

/*
 * This file is part of Laravel Queuing.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Queuing\Queues;

/**
 * This is the queue trait.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
trait QueueTrait
{
    /**
     * The jobs to get pushed.
     *
     * @var array
     */
    protected $jobs = [];

    /**
     * Push a raw payload onto the queue.
     *
     * @param string $payload
     * @param string $queue
     * @param array  $options
     *
     * @return void
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        $this->jobs[] = [
            'type'    => 'push',
            'payload' => $payload,
            'queue'   => $queue,
            'options' => $options,
        ];
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
        $this->jobs[] = [
            'type'  => 'later',
            'delay' => $delay,
            'job'   => $job,
            'data'  => $data,
            'queue' => $queue,
        ];
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
