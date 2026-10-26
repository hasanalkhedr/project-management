<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إضافة سجل حضور'),
            Actions\Action::make('bulk_attendance')
                ->label('تسجيل حضور جماعي')
                ->icon('heroicon-o-users')
                ->url(AttendanceResource::getUrl('bulk'))
                ->color('info'),
        ];
    }
}
