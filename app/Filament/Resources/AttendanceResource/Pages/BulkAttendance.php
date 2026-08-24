<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;

class BulkAttendance extends Page
{
    protected static string $resource = AttendanceResource::class;

    protected static string $view = 'filament.resources.attendance-resource.pages.bulk-attendance';

    public ?array $data = [];

    public function mount(): void
    {
        $employeeId = request()->get('employee_id');
        $departmentId = request()->get('department_id');

        $employeeIds = [];
        if ($employeeId) {
            $employeeIds = [$employeeId];
        } elseif ($departmentId) {
            // Get all employees in this department
            $employeeIds = Employee::where('department_id', $departmentId)
                ->pluck('id')
                ->toArray();
        }

        $this->form->fill([
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'check_in_time' => '09:00',
            'check_out_time' => '17:00',
            'employee_ids' => $employeeIds,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تسجيل الحضور الجماعي')
                    ->description('قم بتسجيل حضور عدة موظفين في وقت واحد')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('من تاريخ')
                                    ->required()
                                    ->displayFormat('d/m/Y')
                                    ->firstDayOfWeek(7)
                                    ->closeOnDateSelection()
                                    ->native(false)
                                    ->default(now()),

                                Forms\Components\DatePicker::make('end_date')
                                    ->label('إلى تاريخ')
                                    ->required()
                                    ->displayFormat('d/m/Y')
                                    ->firstDayOfWeek(7)
                                    ->closeOnDateSelection()
                                    ->native(false)
                                    ->default(now())
                                    ->after('start_date'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TimePicker::make('check_in_time')
                                    ->label('وقت الحضور للجميع')
                                    ->required()
                                    ->seconds(false)
                                    ->native(false)
                                    ->default('09:00'),

                                Forms\Components\TimePicker::make('check_out_time')
                                    ->label('وقت الانصراف للجميع')
                                    ->required()
                                    ->seconds(false)
                                    ->native(false)
                                    ->default('17:00'),
                            ]),

                        Forms\Components\Select::make('employee_ids')
                            ->label('الموظفين')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->required()
                            ->options(function () {
                                return Employee::pluck('name_ar', 'id')
                                    ->toArray();
                            })
                            ->getSearchResultsUsing(function (string $search) {
                                return Employee::where('name_ar', 'like', "%{$search}%")
                                    ->orWhere('name_en', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->pluck('name_ar', 'id')
                                    ->toArray();
                            }),

                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(2)
                            ->placeholder('ملاحظات مشتركة لجميع الموظفين'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label('حفظ')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $startDate = \Carbon\Carbon::parse($data['start_date']);
        $endDate = \Carbon\Carbon::parse($data['end_date']);
        $checkInTime = \Carbon\Carbon::parse($data['check_in_time']);
        $checkOutTime = \Carbon\Carbon::parse($data['check_out_time']);

        // Check for leave conflicts before processing
        $conflictWarnings = [];
        foreach ($data['employee_ids'] as $employeeId) {
            $conflictingLeaves = Leave::where('employee_id', $employeeId)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($q) use ($startDate, $endDate) {
                            $q->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
                })
                ->get();

            if ($conflictingLeaves->count() > 0) {
                $employee = Employee::find($employeeId);
                $conflictWarnings[] = "الموظف {$employee->name_ar} لديه إجازة معتمدة في هذه الفترة";
            }
        }

        if (!empty($conflictWarnings)) {
            Notification::make()
                ->title('تحذير: تضارب مع إجازات معتمدة')
                ->body(implode("\n", $conflictWarnings))
                ->warning()
                ->send();

            // Continue anyway but warn the user
        }

        DB::beginTransaction();
        try {
            $createdCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;

            // Iterate through each date in the range
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                // Skip Friday (day off)
                if ($currentDate->dayOfWeek === 5) {
                    $currentDate->addDay();
                    continue;
                }

                // Combine date with times
                $fullCheckIn = $currentDate->copy()->setTimeFrom($checkInTime);
                $fullCheckOut = $currentDate->copy()->setTimeFrom($checkOutTime);

                // Calculate working hours (same logic as AttendanceResource)
                $workingHours = $fullCheckIn->diffInHours($fullCheckOut);
                $overtimeHours = $workingHours > 8 ? $workingHours - 8 : 0;

                // Calculate late minutes
                $expectedStartTime = \Carbon\Carbon::parse('09:00');
                $fullExpectedStart = $currentDate->copy()->setTimeFrom($expectedStartTime);
                $lateMinutes = $fullCheckIn->gt($fullExpectedStart)
                    ? $fullExpectedStart->diffInMinutes($fullCheckIn)
                    : 0;

                foreach ($data['employee_ids'] as $employeeId) {
                    // Check if attendance already exists for this employee on this date
                    $existingAttendance = Attendance::where('employee_id', $employeeId)
                        ->where('date', $currentDate->format('Y-m-d'))
                        ->first();

                    if ($existingAttendance) {
                        // Skip existing records to avoid duplicates
                        $skippedCount++;
                        continue;
                    } else {
                        // Create new record
                        Attendance::create([
                            'employee_id' => $employeeId,
                            'date' => $currentDate->format('Y-m-d'),
                            'check_in_time' => $fullCheckIn,
                            'check_out_time' => $fullCheckOut,
                            'working_hours' => $workingHours,
                            'overtime_hours' => $overtimeHours,
                            'late_minutes' => $lateMinutes,
                            'is_absent' => false,
                            'notes' => $data['notes'] ?? null,
                        ]);
                        $createdCount++;
                    }
                }

                $currentDate->addDay();
            }

            DB::commit();

            $message = "تم تسجيل الحضور بنجاح!";
            if ($createdCount > 0) {
                $message .= " تم إنشاء {$createdCount} سجل جديد.";
            }
            if ($skippedCount > 0) {
                $message .= " تم تخطي {$skippedCount} سجل موجود مسبقاً.";
            }

            Notification::make()
                ->title($message)
                ->success()
                ->send();

            $this->redirect(AttendanceResource::getUrl('index'));

        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('حدث خطأ أثناء تسجيل الحضور')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
