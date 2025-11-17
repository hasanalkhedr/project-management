<?php

namespace App\Filament\Resources\ProjectContractResource\Pages;

use App\Filament\Resources\ProjectContractResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewContract extends ViewRecord
{
    protected static string $resource = ProjectContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->label('تعديل'),
        ];
    }
}
