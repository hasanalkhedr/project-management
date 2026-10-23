<?php

namespace App\Filament\Vitahr\Resources\WorkLocationResource\Pages;

use App\Filament\Vitahr\Resources\WorkLocationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkLocation extends CreateRecord
{
    protected static string $resource = WorkLocationResource::class;

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
