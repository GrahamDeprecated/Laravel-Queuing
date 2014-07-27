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

use Illuminate\Queue\SyncQueue as LaravelSyncQueue;

/**
 * This is the sync queue class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Queuing/blob/master/LICENSE.md> Apache 2.0
 */
class SyncQueue extends LaravelSyncQueue implements QueueInterface
{
    /**
     * The jobs to get pushed.
     *
     * @type array
     */
    protected $jobs = array();

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
        $this->jobs[] = array(
            'job'  => $job,
            'data' => $data
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
            // process the job
            $this->resolveJob($job['job'], json_encode($job['data']))->fire();
            // remove it from the processing queue
            unset($this->jobs[$id]);
        }
    }
}
