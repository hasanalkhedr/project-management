<?php

namespace App\Filament\Resources\EmployeeContractResource\Pages;

use App\Filament\Resources\EmployeeContractResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeContract extends EditRecord
{
    protected static string $resource = EmployeeContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
     protected function getRedirectUrl(): ?string
    {
        return static::getResource()::getUrl('index');
    }
}
