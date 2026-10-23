<?php

namespace App\Filament\Vitahr\Resources\EmployeeResource\Pages;

use App\Filament\Vitahr\Resources\EmployeeResource;
use App\Models\Vitahr\Employee;
use App\Models\Vitahr\User;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = $this->record->user;

        $data['has_login_account'] = $user !== null;
        $data['login_email'] = $user?->email;
        $data['login_password'] = null;
        $data['password_confirmation'] = null;

        return $data;
    }

    protected function handleRecordUpdate($record, array $data): Employee
{
    $loginEmail = filled($data['login_email'] ?? null) ? $data['login_email'] : null;
    $loginPassword = filled($data['login_password'] ?? null) ? $data['login_password'] : null;
    $passwordConfirmation = filled($data['password_confirmation'] ?? null) ? $data['password_confirmation'] : null;

    // تحقق مسبق من تكرار البريد (تجاهل المستخدم الحالي)
    if ($loginEmail !== null) {
        $existingUserId = $record->user?->id;

        $emailTaken = User::where('email', $loginEmail)
            ->when($existingUserId, fn ($q) => $q->whereKeyNot($existingUserId))
            ->exists();

        if ($emailTaken) {
            throw ValidationException::withMessages([
                'data.login_email' => __('vitahr.email_already_taken'),
            ]);
        }
    }

    if ($loginPassword !== null && $loginEmail === null) {
        throw ValidationException::withMessages([
            'data.login_password' => __('vitahr.password_without_email'),
        ]);
    }

    if (
        $loginPassword !== null &&
        $passwordConfirmation !== null &&
        $loginPassword !== $passwordConfirmation
    ) {
        throw ValidationException::withMessages([
            'data.login_password' => __('vitahr.password_confirmation_mismatch'),
        ]);
    }

    try {
        return DB::connection('vitahr')->transaction(function () use (
            $record,
            $data,
            $loginEmail,
            $loginPassword
        ): Employee {
            unset(
                $data['login_email'],
                $data['login_password'],
                $data['password_confirmation'],
                $data['employee_number_display'],
                $data['has_login_account'],
            );

            // 1. تحديث/إنشاء المستخدم
            if ($record->user) {
                $user = $record->user;
                $user->name = $data['emp_name'] ?? $user->name;

                if ($loginEmail !== null && $loginEmail !== $user->email) {
                    $user->email = $loginEmail;
                }

                if ($loginPassword !== null) {
                    $user->password = $loginPassword; // hashed cast
                }

                $user->save();
            } elseif ($loginEmail !== null && $loginPassword !== null) {
                User::create([
                    'name' => $data['emp_name'] ?? '',
                    'email' => $loginEmail,
                    'password' => $loginPassword,
                    'employee_id' => $record->id,
                ])->assignRole('Employee');
            }

            // 2. تحديث سجل الموظف
            $record->update($data);

            return $record->fresh(['user', 'workLocations']);
        });
    } catch (UniqueConstraintViolationException $e) {
        if (str_contains($e->getMessage(), 'users_email_unique')) {
            throw ValidationException::withMessages([
                'data.login_email' => __('vitahr.email_already_taken'),
            ]);
        }
        throw $e;
    }
}

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
