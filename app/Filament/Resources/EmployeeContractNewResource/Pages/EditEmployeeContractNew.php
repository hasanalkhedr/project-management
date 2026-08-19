<?php

namespace App\Filament\Resources\EmployeeContractNewResource\Pages;

use App\Filament\Resources\EmployeeContractNewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeContractNew extends EditRecord
{
    protected static string $resource = EmployeeContractNewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
