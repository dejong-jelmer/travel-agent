<?php

use App\Console\Commands\AnonymizeOldBookings;
use App\Console\Commands\AnonymizeSpecialRequests;
use App\Console\Commands\GenerateSitemap;
use App\Console\Commands\Newsletter\PurgeUnsubscribedSubscribers;
use App\Console\Commands\PurgeOldTripRequests;
use Illuminate\Support\Facades\Schedule;

Schedule::command(PurgeUnsubscribedSubscribers::class)->monthly();
Schedule::command(AnonymizeOldBookings::class)->yearly();
Schedule::command(AnonymizeSpecialRequests::class)->daily();
Schedule::command(PurgeOldTripRequests::class)->monthly();
Schedule::command(GenerateSitemap::class)->daily();
