<?php

namespace App\Jobs;

use App\Models\Vitahr\EmployeeAccountRequestNotification;
use App\Services\EmployeeAccountRequestNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendEmployeeAccountRequestNotification implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 60, 300];

    public int $uniqueFor = 3600;

    public function uniqueId(): string
    {
        return 'employee-account-request-notification:' . $this->notificationId;
    }
    public function __construct(
        public readonly int $notificationId,
    ) {
    }

    public function handle(
        EmployeeAccountRequestNotificationService $notificationService,
    ): void {
        $notification = EmployeeAccountRequestNotification::query()
            ->find($this->notificationId);

        if ($notification === null) {
            Log::warning(
                'Employee account request notification was not found.',
                ['notification_id' => $this->notificationId],
            );

            return;
        }

        // لا نرسل إلا الإشعارات المعلّقة.
        if ($notification->status !== 'pending') {
            return;
        }

        $notificationService->send($notification);
    }

    public function failed(?Throwable $exception): void
    {
        $notification = EmployeeAccountRequestNotification::query()
            ->find($this->notificationId);

        if ($notification !== null && $notification->status !== 'sent') {
            $notification->update([
                'status' => 'failed',
                'error_message' => mb_substr(
                    $exception?->getMessage()
                    ?? 'The notification job failed.',
                    0,
                    60000,
                ),
            ]);
        }

        Log::error(
            'Employee account request notification job exhausted its retries.',
            [
                'notification_id' => $this->notificationId,
                'error' => $exception?->getMessage(),
            ],
        );
    }
}
