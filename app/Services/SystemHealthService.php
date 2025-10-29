<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDOException;

class SystemHealthService
{
    private int $threshold = 0;
    public function __config()
    {
        $this->threshold = config('queue_warning_threshold');
    }
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
                'message' => __('health.database.connected'),
            ];
        } catch (PDOException $e) {
            Log::error('Database health check failed', ['error' => $e->getMessage()]);

            return [
                'status' => 'error',
                'responseTime' => null,
                'message' => __('health.database.disconnected'),
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
            $mailjetKey = config('services.mailjet.key');
            $mailjetSecret = config('services.mailjet.secret');

            $configured = ! empty($mailjetKey) && ! empty($mailjetSecret);

            if ($configured) {
                return [
                    'status' => 'healthy',
                    'configured' => true,
                    'provider' => 'Mailjet',
                    'message' => __('health.email.configured'),
                ];
            }

            return [
                'status' => 'warning',
                'configured' => false,
                'provider' => 'Mailjet',
                'message' => __('health.email.not_configured'),
            ];
        } catch (Exception $e) {
            Log::error('Email health check failed', ['error' => $e->getMessage()]);

            return [
                'status' => 'error',
                'configured' => false,
                'message' => __('health.email.failed'),
            ];
        }
    }

    /**
     * Check queue worker health
     */
    public function checkQueue(): array
    {
        try {
            $connection = config('queue.default');
            $pendingJobs = 0;

            // Count pending jobs for database queue
            if ($connection === 'database') {
                $pendingJobs = DB::table('jobs')->count();
            }

            // Determine status based on queue size
            if ($pendingJobs > $this->threshold) {
                $status = 'warning';
                $message = __('health.queue.pending', ['jobs' => $pendingJobs]);
            } elseif ($pendingJobs > 0) {
                $status = 'healthy';
                $message = __('health.queue.pending', ['jobs' => $pendingJobs]);
            } else {
                $status = 'healthy';
                $message = __('health.queue.empty');
            }

            return [
                'status' => $status,
                'pendingJobs' => $pendingJobs,
                'connection' => $connection,
                'message' => $message,
            ];
        } catch (Exception $e) {
            Log::error('Queue health check failed', ['error' => $e->getMessage()]);

            return [
                'status' => 'error',
                'pendingJobs' => null,
                'message' => __('health.queue.unavailable'),
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
