<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessQueueManually extends Command
{
    protected $signature = 'queue:process-manual';

    protected $description = 'Process queue jobs without PCNTL';

    public function handle()
    {
        $connection = config('queue.default');
        $queue = 'default';

        $job = DB::table('jobs')
            ->where('queue', $queue)
            ->where('available_at', '<=', now()->timestamp)
            ->orderBy('id')
            ->first();

        if (! $job) {
            $this->info('No jobs in queue');

            return 0;
        }

        try {
            DB::table('jobs')->where('id', $job->id)->delete();

            $payload = json_decode($job->payload, true);
            $command = unserialize($payload['data']['command']);

            dispatch_sync($command);

            $this->info('Job processed successfully');
        } catch (\Exception $e) {
            DB::table('failed_jobs')->insert([
                'connection' => $connection,
                'queue' => $queue,
                'payload' => $job->payload,
                'exception' => $e->getMessage(),
                'failed_at' => now(),
            ]);

            $this->error('Job failed: '.$e->getMessage());
        }

        return 0;
    }
}
