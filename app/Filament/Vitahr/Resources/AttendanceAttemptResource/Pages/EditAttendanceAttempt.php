<?php

namespace App\Filament\Vitahr\Resources\AttendanceAttemptResource\Pages;

use App\Filament\Vitahr\Resources\AttendanceAttemptResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttendanceAttempt extends EditRecord
{
    protected static string $resource = AttendanceAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
