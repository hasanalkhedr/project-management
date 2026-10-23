<?php

namespace App\Filament\Vitahr\Resources\EmployeeDeviceResource\Pages;

use App\Filament\Vitahr\Resources\EmployeeDeviceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeDevice extends EditRecord
{
    protected static string $resource = EmployeeDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
