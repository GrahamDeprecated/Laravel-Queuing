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

use Illuminate\Queue\SyncQueue as LaravelSyncQueue;

/**
 * This is the sync queue class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class SyncQueue extends LaravelSyncQueue implements QueueInterface
{
    /**
     * The jobs to get pushed.
     *
     * @var array
     */
    protected $jobs = [];

    /**
     * Push a new job onto the queue.
     *
     * @param string $job
     * @param mixed  $data
     * @param string $queue
     *
     * @return void
     */
    public function push($job, $data = '', $queue = null)
    {
        $this->jobs[] = [
            'job'  => $job,
            'data' => $data,
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
            // process the job
            $this->resolveJob($job['job'], json_encode($job['data']))->fire();
            // remove it from the processing queue
            unset($this->jobs[$id]);
        }
    }
}
