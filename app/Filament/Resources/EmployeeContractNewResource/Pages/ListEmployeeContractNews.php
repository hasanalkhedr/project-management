<?php

namespace App\Filament\Resources\EmployeeContractNewResource\Pages;

use App\Filament\Resources\EmployeeContractNewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeContractNews extends ListRecords
{
    protected static string $resource = EmployeeContractNewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
