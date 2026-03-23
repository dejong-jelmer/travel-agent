<?php

use App\Console\Commands\AnonymizeOldBookings;
use App\Console\Commands\Newsletter\PurgeUnsubscribedSubscribers;
use Illuminate\Support\Facades\Schedule;


Schedule::command(PurgeUnsubscribedSubscribers::class)->monthly();
Schedule::command(AnonymizeOldBookings::class)->yearly();
