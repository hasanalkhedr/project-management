<?php

namespace App\Filament\Vitahr\Resources\EmployeeResource\Pages;

use App\Filament\Vitahr\Resources\EmployeeResource;
use App\Models\Vitahr\Employee;
use App\Models\Vitahr\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function handleRecordCreation(array $data): Employee
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
            unset(
                $data['login_email'],
                $data['login_password'],
                $data['password_confirmation'],
                $data['employee_number_display'],
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

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
