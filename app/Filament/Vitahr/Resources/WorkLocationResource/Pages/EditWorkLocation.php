<?php

namespace App\Filament\Vitahr\Resources\WorkLocationResource\Pages;

use App\Filament\Vitahr\Resources\WorkLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkLocation extends EditRecord
{
    protected static string $resource = WorkLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
