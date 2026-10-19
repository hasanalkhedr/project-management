<?php

namespace App\Filament\Vitahr\Resources\EmployeeAccountRequestResource\Pages;

use App\Filament\Vitahr\Resources\EmployeeAccountRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeAccountRequests extends ListRecords
{
    protected static string $resource = EmployeeAccountRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
