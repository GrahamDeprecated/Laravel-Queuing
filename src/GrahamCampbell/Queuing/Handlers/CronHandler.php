<?php namespace GrahamCampbell\Queuing\Handlers;

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

use GrahamCampbell\Queuing\Facades\Cron;
use GrahamCampbell\Queuing\Facades\JobProvider;

class CronHandler extends BaseHandler {

    /**
     * Run the task (called by BaseHandler).
     *
     * @return void
     */
    protected function run() {
        $data = $this->data;
        JobProvider::clearOldJobs();
        foreach ($data['tasks'] as $task) {
            $task();
        }
    }

    /**
     * Run after a job success (called by BaseHandler).
     *
     * @return void
     */
    protected function afterSuccess() {
        Cron::start();
    }

    /**
     * Run after a job abortion (called by BaseHandler).
     *
     * @return void
     */
    protected function afterAbortion() {
        if ($this->model) {
            Cron::start(500);
        }
    }
}
