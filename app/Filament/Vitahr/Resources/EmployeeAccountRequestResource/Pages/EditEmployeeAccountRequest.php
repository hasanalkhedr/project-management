<?php

namespace App\Filament\Vitahr\Resources\EmployeeAccountRequestResource\Pages;

use App\Filament\Vitahr\Resources\EmployeeAccountRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeAccountRequest extends EditRecord
{
    protected static string $resource = EmployeeAccountRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
