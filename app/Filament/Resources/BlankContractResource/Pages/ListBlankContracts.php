<?php

namespace App\Filament\Resources\BlankContractResource\Pages;

use App\Filament\Resources\BlankContractResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBlankContracts extends ListRecords
{
    protected static string $resource = BlankContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('إنشاء عقد جديد'),
        ];
    }
}
