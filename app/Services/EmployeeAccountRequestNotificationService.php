<?php

namespace App\Services;

use App\Models\Vitahr\EmployeeAccountRequestNotification;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Throwable;

use Kreait\Firebase\Exception\Messaging\NotFound;
use Kreait\Firebase\Exception\Messaging\InvalidMessage;

class EmployeeAccountRequestNotificationService
{
    public function __construct(
        private readonly Messaging $messaging,
    ) {
    }



    public function prepareDecision(
        \App\Models\Vitahr\EmployeeAccountRequest $accountRequest,
        string $decision,
    ): EmployeeAccountRequestNotification {
        $approved = $decision === \App\Models\Vitahr\EmployeeAccountRequest::STATUS_APPROVED;

        return $accountRequest->notifications()->create([
            'type' => $approved
                ? 'registration_approved'
                : 'registration_rejected',
            'channel' => 'fcm',
            'status' => 'pending',
            'fcm_token' => $accountRequest->fcm_token,
            'payload' => [
                'title' => $approved
                    ? __('vitahr.Registration approved')
                    : __('vitahr.Registration rejected'),
                'body' => $approved
                    ? __('vitahr.Your employee account registration has been approved.')
                    : __('vitahr.Your employee account registration has been rejected.'),
                'data' => [
                    'type' => $approved
                        ? 'registration_approved'
                        : 'registration_rejected',
                    'status' => $decision,
                ],
            ],
        ]);
    }


    /**
     * Send an already-created notification record.
     *
     * Uses the FCM token snapshot saved when the notification was prepared.
     */
    public function send(
        EmployeeAccountRequestNotification $notification,
    ): EmployeeAccountRequestNotification {
        $token = $notification->fcm_token;
        $payload = $notification->payload ?? [];


        if (blank($token)) {
            return $this->markPermanentlyFailed(
                $notification,
                'FCM token is missing from the registration request.',
            );
        }


        $title = $payload['title'] ?? __('vitahr.Account registration update');
        $body = $payload['body'] ?? __('vitahr.Your registration status has changed.');
        $data = $payload['data'] ?? [];

        try {
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(
                    FirebaseNotification::create($title, $body),
                )
                ->withData($this->stringifyData($data));

            $this->messaging->send($message);

            $notification->update([
                'status' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);
        } catch (
            NotFound |
            InvalidMessage $exception
        ) {
            report($exception);

            return $this->markPermanentlyFailed(
                $notification,
                mb_substr($exception->getMessage(), 0, 60000),
            );
        } catch (Throwable $exception) {
            report($exception);

            $this->markFailed(
                $notification,
                mb_substr($exception->getMessage(), 0, 60000),
            );

            throw $exception;
        }

        return $notification->refresh();
    }

    private function markFailed(
        EmployeeAccountRequestNotification $notification,
        string $error,
    ): EmployeeAccountRequestNotification {
        $notification->update([
            'error_message' => $error,
        ]);

        return $notification->refresh();
    }


    private function markPermanentlyFailed(
        EmployeeAccountRequestNotification $notification,
        string $error,
    ): EmployeeAccountRequestNotification {
        $notification->update([
            'status' => 'failed',
            'error_message' => $error,
        ]);

        return $notification->refresh();
    }

    /**
     * FCM data payload values must be strings.
     *
     * @param array<string, mixed> $data
     * @return array<string, string>
     */
    private function stringifyData(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $result[(string) $key] = (string) $value;
            }
        }

        return $result;
    }
}
