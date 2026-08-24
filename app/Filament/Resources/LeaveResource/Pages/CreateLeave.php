<?php

namespace App\Filament\Resources\LeaveResource\Pages;

use App\Filament\Resources\LeaveResource;
use App\Models\Attendance;
use App\Models\Leave;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateLeave extends CreateRecord
{
    protected static string $resource = LeaveResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Calculate total days if not already calculated
        if (isset($data['start_date']) && isset($data['end_date']) && !isset($data['total_days'])) {
            $start = \Carbon\Carbon::parse($data['start_date']);
            $end = \Carbon\Carbon::parse($data['end_date']);
            $data['total_days'] = $start->diffInDays($end) + 1;
        }

        return $data;
    }

    protected function beforeCreate(): void
    {
        $data = $this->form->getState();

        // Check for attendance conflicts during leave period
        $conflictingAttendances = Attendance::where('employee_id', $data['employee_id'])
            ->whereBetween('date', [$data['start_date'], $data['end_date']])
            ->where('is_absent', false)
            ->get();

        if ($conflictingAttendances->count() > 0) {
            $dates = $conflictingAttendances->pluck('date')->map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('d/m/Y');
            })->implode(', ');

            Notification::make()
                ->title('تضارب مع سجلات الحضور')
                ->body("يوجد سجلات حضور للموظف في التواريخ: {$dates}. يرجى مراجعة سجلات الحضور قبل إنشاء الإجازة.")
                ->warning()
                ->send();

            $this->halt();
        }
    }
}
