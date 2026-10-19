<?php

namespace App\Filament\Vitahr\Resources\EmployeeResource\Pages;

use App\Filament\Vitahr\Resources\EmployeeResource;
use App\Models\Vitahr\Employee;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
use Illuminate\Support\Facades\DB;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeFill(
        array $data
    ): array {
        $user = $this->record->user;

        //$data['employee_name'] = $user?->name;
        $data['login_email'] = $user?->email;

        return $data;
    }

    protected function mutateFormDataBeforeSave(
        array $data
    ): array {
        /*
         * employee_name is a form-only field.
         *
         * It must not be sent to Employee::update().
         */
        //unset($data['employee_name']);

        return $data;
    }

    protected function handleRecordUpdate(
        $record,
        array $data
    ): Employee {
        return DB::connection('vitahr')->transaction(function () use ($record, $data) {
            /*
             * Update Employee name in users table.
             */
                $record->user()->update([
                    'name' => $this->data['emp_name'],
                ]);

            /*
             * Update Employee record.
             */
            $record->update($data);

            return $record->fresh([
                'user',
                'workLocations',
            ]);
        });
    }

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
