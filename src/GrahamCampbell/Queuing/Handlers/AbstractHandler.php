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

namespace GrahamCampbell\Queuing\Handlers;

use Illuminate\Support\Facades\Log;
use GrahamCampbell\Queuing\Facades\JobProvider;

/**
 * This is the abstract handler class.
 *
 * @package    Laravel-Queuing
 * @author     Graham Campbell
 * @copyright  Copyright 2013 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Queuing/blob/develop/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Queuing
 */
abstract class AbstractHandler
{
    /**
     * The maximum number of tries.
     *
     * @var array
     */
    protected $maxtries = 6;

    /**
     * The current number of tries.
     *
     * @var array
     */
    protected $tries = 1;

    /**
     * The job id.
     *
     * @var int
     */
    protected $id;

    /**
     * The job task.
     *
     * @var string
     */
    protected $task;

    /**
     * The job method.
     *
     * @var string
     */
    protected $method;

    /**
     * The job model.
     *
     * @var mixed
     */
    protected $model;

    /**
     * The handler job.
     *
     * @var mixed
     */
    protected $job;

     /**
     * The handler data.
     *
     * @var array
     */
    protected $data;

    /**
     * Constructor. Runs the init method.
     *
     * @return void
     */
    public function __construct()
    {
        $this->init(); // unprotected against exceptions
    }

    /**
     * Fire method (called by Laravel).
     *
     * @return void
     */
    public function fire($job, $data)
    {
        // load job details and data to the class
        $this->job = $job;
        unset($job);
        $this->data = $data;
        unset($data);
        $this->task = get_class($this);
        $this->method = get_class($this->job);

        if ($this->method == 'Illuminate\Queue\Jobs\SyncJob') {
            // log the job start
            Log::debug($this->task.' has started execution of a sync job');
        } else {
            // load job the job id to the class
            $this->id = $this->data['model_id'];

            // log the job start
            Log::debug($this->task.' has started execution of job '.$this->id);

            // check if there is a job model
            try {
                $this->model = JobProvider::find($this->id);
            } catch (\Exception $e) {
                return $this->abort($this->task.' has aborted because the job model was inaccessible');
            }

            // if there's not model, then the job must have been cancelled
            if (!is_object($this->model)) {
                return $this->abort($this->task.' has aborted because the job was marked as cancelled');
            } else {
                if (!is_a($this->model, 'GrahamCampbell\Queuing\Models\Job')) {
                    return $this->abort($this->task.' has aborted because the job was marked as cancelled');
                }
            }

            // check the model
            try {
                if ($this->model->getId() !== $this->id) {
                    throw new \Exception('Bad Id');
                }
                if ($this->model->getTask() !== $this->task) {
                    throw new \Exception('Bad Task');
                }
            } catch (\Exception $e) {
                return $this->abort($this->task.' has aborted because the job model was invalid');
            }

            // increment tries
            try {
                $this->tries = $this->model->getTries() + 1;
                $this->model->tries = $this->tries;
                $this->model->save();
            } catch (\Exception $e) {
                return $this->abort($this->task.' has aborted because the job model was inaccessible');
            }
        }

        // run the before method
        try {
            $this->before();
        } catch (\Exception $e) {
            return $this->fail($e);
        }

        // run the handler
        try {
            $this->run();
        } catch (\Exception $e) {
            return $this->fail($e);
        }

        // finish up
        $this->success();
    }

    /**
     * Success method (called on success).
     *
     * @return void
     */
    protected function success()
    {
        // remove the job from the queue
        try {
            $this->job->delete();
        } catch (\Exception $e) {
            Log::error($e);
        }

        // remove the job from the database
        if (is_object($this->model)) {
            if (is_a($this->model, 'GrahamCampbell\Queuing\Models\Job')) {
                try {
                    $this->model->delete();
                } catch (\Exception $e) {
                    Log::error($e);
                }
            }
        }

        // run the afterSuccess method
        try {
            $this->afterSuccess();
        } catch (\Exception $e) {
            Log::error($e);
        }

        // log the success
        Log::info($this->task.' has completed successfully');
    }

    /**
     * Failure method (called on failure).
     *
     * @return void
     */
    protected function fail($exception = null)
    {
        // run the afterFailure method
        try {
            $this->afterFailure();
        } catch (\Exception $e) {
            Log::error($e);
        }

        // log the error
        if ($exception) {
            Log::critical($exception);
        } else {
            Log::critical($this->task.' has failed without an exception to log');
        }

        // attempt to retry
        if ($this->method == 'Illuminate\Queue\Jobs\BeanstalkdJob' || $this->method == 'Illuminate\Queue\Jobs\RedisJob') {
            // abort if we have retried too many times
            if ($this->tries >= $this->maxtries) {
                return $this->abort($this->task.' has aborted after failing '.$this->tries.' times');
            } else {
                // wait x seconds, then push back to queue
                try {
                    $this->job->release(4*$this->tries);
                } catch (\Exception $e) {
                    Log::critical($e);
                    return $this->abort($this->task.' has aborted after failing to repush to the queue');
                }
            }
        } elseif ($this->method != 'Illuminate\Queue\Jobs\SyncJob') {
            // abort the sync job
            return $this->abort($this->task.' has aborted as a sync job');
        } else {
            // throw an exception in order to push back to queue
            throw new \Exception($this->task.' has failed with '.$this->method);
        }
    }

    /**
     * Abortion method (called on abortion).
     *
     * @return void
     */
    protected function abort($message = null)
    {
        // run the afterAbortion method
        try {
            $this->afterAbortion();
        } catch (\Exception $e) {
            Log::error($e);
        }

        // remove the job from the queue
        try {
            $this->job->delete();
        } catch (\Exception $e) {
            Log::error($e);
        }

        // remove the job from the database
        if (is_object($this->model)) {
            if (is_a($this->model, 'GrahamCampbell\Queuing\Models\Job')) {
                try {
                    $this->model->delete();
                } catch (\Exception $e) {
                    Log::error($e);
                }
            }
        }

        if ($this->method == 'Illuminate\Queue\Jobs\BeanstalkdJob' || $this->method == 'Illuminate\Queue\Jobs\RedisJob') {
            // log the message
            if ($message) {
                Log::critical($message);
            } else {
                Log::critical($this->task.' has aborted without a message');
            }
        } else {
            // make sure the queue knows the job aborted
            if ($message) {
                throw new \Exception($message);
            } else {
                throw new \Exception($this->task.' has aborted without a message');
            }
        }
    }

    /**
     * Initialisation for the job.
     *
     * @return void
     */
    protected function init()
    {
        // can be overwritten by extending class
    }

    /**
     * Run on construction.
     *
     * @return void
     */
    protected function before()
    {
        // can be overwritten by extending class
    }

    /**
     * Run after a job success.
     *
     * @return void
     */
    protected function afterSuccess()
    {
        // can be overwritten by extending class
    }

    /**
     * Run after a job failure.
     *
     * @return void
     */
    protected function afterFailure()
    {
        // can be overwritten by extending class
    }

    /**
     * Run after a job abortion.
     *
     * @return void
     */
    protected function afterAbortion()
    {
        // can be overwritten by extending class
    }

    /**
     * Run the job.
     *
     * @return void
     */
    abstract protected function run();
}
