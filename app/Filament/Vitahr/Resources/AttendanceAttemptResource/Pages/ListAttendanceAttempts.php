<?php

namespace App\Filament\Vitahr\Resources\AttendanceAttemptResource\Pages;

use App\Filament\Vitahr\Resources\AttendanceAttemptResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceAttempts extends ListRecords
{
    protected static string $resource = AttendanceAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
