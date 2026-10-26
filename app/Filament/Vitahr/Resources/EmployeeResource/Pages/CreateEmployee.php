<?php

namespace App\Filament\Vitahr\Resources\EmployeeResource\Pages;

use App\Filament\Vitahr\Resources\EmployeeResource;
use App\Models\Vitahr\Employee;
use App\Models\Vitahr\User;
use Filament\Resources\Pages\CreateRecord;
<<<<<<< HEAD
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
=======
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
>>>>>>> 86eefc5d13472f7be23f475d108a730aa12de8b0
use RuntimeException;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function handleRecordCreation(array $data): Employee
<<<<<<< HEAD
{
    $loginEmail = filled($data['login_email'] ?? null) ? $data['login_email'] : null;
    $loginPassword = filled($data['login_password'] ?? null) ? $data['login_password'] : null;
    $passwordConfirmation = filled($data['password_confirmation'] ?? null) ? $data['password_confirmation'] : null;

    // تحقق مسبق من تكرار البريد
    if ($loginEmail !== null && User::where('email', $loginEmail)->exists()) {
        throw ValidationException::withMessages([
            'data.login_email' => __('vitahr.email_already_taken'),
        ]);
    }

    if (
        ($loginEmail !== null && $loginPassword === null) ||
        ($loginEmail === null && $loginPassword !== null)
    ) {
        throw ValidationException::withMessages([
            'data.login_email' => __('vitahr.login_credentials_incomplete'),
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
            $data,
            $loginEmail,
            $loginPassword
        ): Employee {
=======
    {
        return DB::connection('vitahr')->transaction(function () use ($data): Employee {
            $loginEmail = filled($data['login_email'] ?? null)
                ? $data['login_email']
                : null;

            $loginPassword = filled($data['login_password'] ?? null)
                ? $data['login_password']
                : null;

            /*
             * These fields belong to the Filament form,
             * not to the employees table.
             */
>>>>>>> 86eefc5d13472f7be23f475d108a730aa12de8b0
            unset(
                $data['login_email'],
                $data['login_password'],
                $data['password_confirmation'],
                $data['employee_number_display'],
<<<<<<< HEAD
                $data['has_login_account'],
            );

            // 1. إنشاء الموظف أولاً
            $data['employee_number'] = 'TMP-' . Str::uuid();
            $employee = Employee::create($data);
            $employee_id = $employee->id;

            $employee->update([
                'employee_number' => sprintf('EMP-%06d', $employee->id),
            ]);

            // 2. إنشاء المستخدم مرتبطاً بالموظف
            if ($loginEmail !== null && $loginPassword !== null) {
                $user = User::create([
                    'name' => $employee->emp_name,
                    'email' => $loginEmail,
                    'password' => $loginPassword, // casts() يعمل hash تلقائياً
                    'employee_id' => $employee_id,
                ]);

                $user->assignRole('Employee');
            }

            return $employee->fresh();
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
=======
            );

            /*
             * A login account is optional.
             * If the administrator starts configuring one,
             * both email and password must be provided.
             */
            if (
                ($loginEmail !== null && $loginPassword === null) ||
                ($loginEmail === null && $loginPassword !== null)
            ) {
                throw new RuntimeException(
                    'To create a login account, provide both email and password. ' .
                    'Otherwise, leave both fields empty.'
                );
            }

            /*
             * Create a User only when login credentials
             * were explicitly provided.
             */
            if ($loginEmail !== null && $loginPassword !== null) {
                $user = User::create([
                    'name' => $data['emp_name'],
                    'email' => $loginEmail,
                    'password' => Hash::make($loginPassword),
                ]);

                $user->assignRole('Employee');

                $data['user_id'] = $user->id;
            } else {
                $data['user_id'] = null;
            }

            /*
             * Generate a temporary unique employee number,
             * then replace it using the generated employee ID.
             */
            $data['employee_number'] = 'TMP-' . Str::uuid();

            $employee = Employee::create($data);

            $employee->update([
                'employee_number' => sprintf(
                    'EMP-%06d',
                    $employee->id
                ),
            ]);

            return $employee->fresh();
        });
    }
>>>>>>> 86eefc5d13472f7be23f475d108a730aa12de8b0

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
