<?php

namespace App\Services;

use App\Models\Vitahr\EmployeeAccountRequest;
use App\Models\Vitahr\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;
use App\Jobs\SendEmployeeAccountRequestNotification;
class EmployeeAccountRequestReviewService
{
    public function __construct(
        private readonly EmployeeAccountRequestNotificationService $notifications,
    ) {
    }

    public function approve(
        int $requestId,
        User $reviewer
    ): EmployeeAccountRequest {
        return DB::connection('vitahr')->transaction(function () use ($requestId, $reviewer) {
            $accountRequest = EmployeeAccountRequest::query()
                ->lockForUpdate()
                ->findOrFail($requestId);

            $this->ensurePending($accountRequest);

            $employee = $accountRequest->employee()
                ->lockForUpdate()
                ->firstOrFail();

            if (!$employee->is_active) {
                throw ValidationException::withMessages([
                    'request' => [
                        __('vitahr.Employee account is inactive.'),
                    ],
                ]);
            }

            if ($employee->user_id !== null) {
                throw ValidationException::withMessages([
                    'request' => [
                        __('vitahr.Employee already has a user account.'),
                    ],
                ]);
            }

            if (
                $accountRequest->active_email === null
                || strcasecmp(
                    $accountRequest->active_email,
                    $accountRequest->email,
                ) !== 0
            ) {
                throw ValidationException::withMessages([
                    'request' => [
                        __('vitahr.The email reservation is invalid.'),
                    ],
                ]);
            }

            if (
                User::query()
                    ->where('email', $accountRequest->email)
                    ->exists()
            ) {
                throw ValidationException::withMessages([
                    'request' => [
                        __('validation.unique', ['attribute' => 'email']),
                    ],
                ]);
            }

            try {
                $user = User::query()->create([
                    'name' => $employee->emp_name,
                    'email' => $accountRequest->email,
                    'password' => $accountRequest->password_hash,
                ]);
            } catch (QueryException $exception) {
                $sqlState = $exception->errorInfo[0] ?? null;
                $driverCode = (int) ($exception->errorInfo[1] ?? 0);
                $message = $exception->getMessage();

                $isDuplicateEmail = $sqlState === '23000'
                    && $driverCode === 1062
                    && str_contains($message, 'users_email_unique');

                if (!$isDuplicateEmail) {
                    throw $exception;
                }

                throw ValidationException::withMessages([
                    'request' => [
                        __('validation.unique', ['attribute' => 'email']),
                    ],
                ]);
            }

            $user->assignVitahrRole('Employee');

            $employee->user_id = $user->id;
            $employee->save();

            $accountRequest->status =
                EmployeeAccountRequest::STATUS_APPROVED;
            $accountRequest->reviewed_by = $reviewer->id;
            $accountRequest->reviewed_at = now();
            $accountRequest->save();

            $notification = $this->notifications->prepareDecision(
                $accountRequest,
                EmployeeAccountRequest::STATUS_APPROVED,
            );

            // Invalidate the registration token-update secret after preparing
// the notification with the latest available FCM token.
            $accountRequest->fcm_token_update_secret_hash = null;
            $accountRequest->save();

            $this->scheduleNotificationAfterCommit($notification->id);

            return $accountRequest->refresh();
        });
    }

    public function reject(
        int $requestId,
        User $reviewer,
        string $reason
    ): EmployeeAccountRequest {
        return DB::connection('vitahr')->transaction(function () use ($requestId, $reviewer, $reason, ) {
            $accountRequest = EmployeeAccountRequest::query()
                ->lockForUpdate()
                ->findOrFail($requestId);

            $this->ensurePending($accountRequest);

            $accountRequest->status =
                EmployeeAccountRequest::STATUS_REJECTED;
            $accountRequest->active_email = null;
            $accountRequest->reviewed_by = $reviewer->id;
            $accountRequest->reviewed_at = now();
            $accountRequest->rejection_reason = trim($reason);
            $accountRequest->save();

            $notification = $this->notifications->prepareDecision(
                $accountRequest,
                EmployeeAccountRequest::STATUS_REJECTED,
            );

            // Invalidate the registration token-update secret after preparing
// the notification with the latest available FCM token.
            $accountRequest->fcm_token_update_secret_hash = null;
            $accountRequest->save();

            $this->scheduleNotificationAfterCommit($notification->id);

            return $accountRequest->refresh();
        });
    }

    private function scheduleNotificationAfterCommit(int $notificationId): void
    {
        DB::connection('vitahr')->afterCommit(function () use ($notificationId): void {
            try {
                SendEmployeeAccountRequestNotification::dispatch(
                    $notificationId,
                );
            } catch (Throwable $exception) {
                report($exception);

                Log::error(
                    'Failed to queue employee account request notification.',
                    [
                        'notification_id' => $notificationId,
                        'error' => $exception->getMessage(),
                    ],
                );
            }
        });
    }

    private function ensurePending(
        EmployeeAccountRequest $accountRequest,
    ): void {
        if (
            $accountRequest->status
            !== EmployeeAccountRequest::STATUS_PENDING
        ) {
            throw ValidationException::withMessages([
                'request' => [
                    __('vitahr.This registration request has already been reviewed.'),
                ],
            ]);
        }
    }
}
