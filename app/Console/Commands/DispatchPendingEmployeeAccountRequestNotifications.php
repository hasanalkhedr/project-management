<?php

namespace App\Console\Commands;

use App\Jobs\SendEmployeeAccountRequestNotification;
use App\Models\Vitahr\EmployeeAccountRequestNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class DispatchPendingEmployeeAccountRequestNotifications extends Command
{
    protected $signature = 'vitahr:dispatch-pending-account-notifications
                            {--limit=100 : Maximum number of notifications to dispatch}';

    protected $description = 'Dispatch pending employee account request notifications to the queue';

    public function handle(): int
    {
        $limit = max(1, min((int) $this->option('limit'), 1000));

        // Mark pending notifications without a usable FCM token as failed.
        // These records cannot be delivered by FCM and must not remain pending.
        EmployeeAccountRequestNotification::query()
            ->where('status', 'pending')
            ->where(function ($query): void {
                $query->whereNull('fcm_token')
                    ->orWhere('fcm_token', '');
            })
            ->limit($limit)
            ->get()
            ->each(function (
                EmployeeAccountRequestNotification $notification,
            ): void {
                $notification->update([
                    'status' => 'failed',
                    'error_message' => 'FCM token is missing.',
                ]);
            });

        $notifications = EmployeeAccountRequestNotification::query()
            ->where('status', 'pending')
            ->whereNotNull('fcm_token')
            ->where('fcm_token', '<>', '')
            ->orderBy('id')
            ->limit($limit)
            ->pluck('id');

        $dispatched = 0;
        $failed = 0;

        foreach ($notifications as $notificationId) {
            try {
                SendEmployeeAccountRequestNotification::dispatch(
                    (int) $notificationId,
                );

                $dispatched++;
            } catch (Throwable $exception) {
                report($exception);

                Log::error(
                    'Failed to dispatch a pending employee account request notification.',
                    [
                        'notification_id' => $notificationId,
                        'error' => $exception->getMessage(),
                    ],
                );

                $failed++;
            }
        }

        $this->info("Dispatched: {$dispatched}");
        $this->info("Dispatch failures: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
