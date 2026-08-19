<?php

namespace App\Filament\Resources\EmployeeContractNewResource\Pages;

use App\Filament\Resources\EmployeeContractNewResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployeeContractNew extends CreateRecord
{
    protected static string $resource = EmployeeContractNewResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Capture employee_id from URL query parameter
        if (request()->has('employee_id')) {
            $data['employee_id'] = request()->get('employee_id');
        }
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
