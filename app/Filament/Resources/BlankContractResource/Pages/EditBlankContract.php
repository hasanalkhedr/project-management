<?php

namespace App\Filament\Resources\BlankContractResource\Pages;

use App\Filament\Resources\BlankContractResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlankContract extends EditRecord
{
    protected static string $resource = BlankContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
