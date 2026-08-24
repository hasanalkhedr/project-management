<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Models\Attendance;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function beforeCreate(): void
    {
        $data = $this->form->getState();

        // Check if attendance already exists for this employee on this date
        $existingAttendance = Attendance::where('employee_id', $data['employee_id'])
            ->where('date', $data['date'])
            ->first();

        if ($existingAttendance) {
            Notification::make()
                ->title('سجل حضور موجود مسبقاً')
                ->body('يوجد سجل حضور لهذا الموظف في هذا التاريخ. يرجى تعديل السجل الموجود بدلاً من إنشاء سجل جديد.')
                ->warning()
                ->send();

            $this->halt();
        }

        // Check for leave conflicts on this date
        $conflictingLeave = \App\Models\Leave::where('employee_id', $data['employee_id'])
            ->where('start_date', '<=', $data['date'])
            ->where('end_date', '>=', $data['date'])
            ->first();

        if ($conflictingLeave) {
            Notification::make()
                ->title('تضارب مع إجازة معتمدة')
                ->body('يوجد إجازة معتمدة للموظف في هذا التاريخ. يرجى مراجعة الإجازات قبل تسجيل الحضور.')
                ->warning()
                ->send();

            $this->halt();
        }
    }
}
