<?php

/*
 * This file is part of Laravel Queuing.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Queuing\Commands;

use Illuminate\Console\Command;

/**
 * This is the queue iron command class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class QueueIron extends Command
{
    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'queue:iron';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Sets up IronMQ subscriptions';

    /**
     * Run the commend.
     *
     * @return void
     */
    public function fire()
    {
        $this->line('Setting up iron queueing...');

        try {
            $this->call('queue:subscribe', [
                'queue' => $this->laravel['config']['queue.connections.iron.queue'],
                'url'   => $this->laravel['url']->to('queue/receive'),
            ]);
            $this->info('Queueing is now setup!');
        } catch (\Exception $e) {
            $this->error('Iron queuing could not be setup!');
        }
    }
}
