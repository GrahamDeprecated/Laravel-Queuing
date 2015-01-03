<?php

/*
 * This file is part of Laravel Queuing.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;

Route::post('queue/receive', function () {
    return Queue::connection('iron')->marshal();
});
