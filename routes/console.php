<?php

use App\Console\Commands\AnonymizeOldBookings;
use App\Console\Commands\AnonymizeSpecialRequests;
use App\Console\Commands\Newsletter\PurgeUnsubscribedSubscribers;
use App\Console\Commands\PurgeOldTripRequests;
use App\Console\Commands\SendDepartureDigest;
use Illuminate\Support\Facades\Schedule;

Schedule::command(PurgeUnsubscribedSubscribers::class)->monthly();
Schedule::command(AnonymizeOldBookings::class)->yearly();
Schedule::command(AnonymizeSpecialRequests::class)->daily();
Schedule::command(PurgeOldTripRequests::class)->monthly();
Schedule::command(SendDepartureDigest::class)->dailyAt('07:00')->timezone('Europe/Amsterdam');
