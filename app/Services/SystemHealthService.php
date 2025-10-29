<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Config;
use Exception;

class SystemHealthService
{
    /**
     * Check database connection health
     */
    public function checkDatabase(): array
    {
        try {
            $startTime = microtime(true);
            DB::connection()->getPdo();
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'status' => 'healthy',
                'responseTime' => $responseTime,
                'message' => 'Verbonden met database',
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'responseTime' => null,
                'message' => 'Database niet bereikbaar',
            ];
        }
    }

    /**
     * Check email service configuration
     */
    public function checkEmail(): array
    {
        try {
            // Check for Mailjet API credentials in environment
            $mailjetKey = env('MAILJET_API_KEY');
            $mailjetSecret = env('MAILJET_API_SECRET');

            $configured = !empty($mailjetKey) && !empty($mailjetSecret);

            if ($configured) {
                return [
                    'status' => 'healthy',
                    'configured' => true,
                    'provider' => 'Mailjet',
                    'message' => 'Mailjet API geconfigureerd',
                ];
            }

            return [
                'status' => 'warning',
                'configured' => false,
                'provider' => 'Mailjet',
                'message' => 'Mailjet API credentials ontbreken',
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'configured' => false,
                'message' => 'Email service check failed',
            ];
        }
    }

    /**
     * Check queue worker health
     */
    public function checkQueue(): array
    {
        try {
            $connection = Config::get('queue.default');
            $pendingJobs = 0;

            // Count pending jobs for database queue
            if ($connection === 'database') {
                $pendingJobs = DB::table('jobs')->count();
            }

            // Determine status based on queue size
            if ($pendingJobs > 100) {
                $status = 'warning';
                $message = "{$pendingJobs} jobs wachten (hoge belasting)";
            } elseif ($pendingJobs > 0) {
                $status = 'healthy';
                $message = "{$pendingJobs} jobs in wachtrij";
            } else {
                $status = 'healthy';
                $message = 'Queue is leeg';
            }

            return [
                'status' => $status,
                'pendingJobs' => $pendingJobs,
                'connection' => $connection,
                'message' => $message,
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'pendingJobs' => null,
                'message' => 'Queue niet bereikbaar',
            ];
        }
    }

    /**
     * Get all system health checks
     */
    public function getAllChecks(): array
    {
        return [
            'database' => $this->checkDatabase(),
            'email' => $this->checkEmail(),
            'queue' => $this->checkQueue(),
            'lastChecked' => now()->toIso8601String(),
        ];
    }
}
