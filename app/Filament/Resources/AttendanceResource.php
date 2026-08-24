<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'الحضور والانصراف';

    protected static ?string $modelLabel = 'سجل حضور';

    protected static ?string $pluralModelLabel = 'سجلات الحضور';

    protected static ?string $navigationGroup = 'إدارة الموارد البشرية';

    protected static ?int $navigationSort = 25;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الحضور')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label('الموظف')
                            ->relationship('employee', 'name_ar')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),

                        Forms\Components\DatePicker::make('date')
                            ->label('التاريخ')
                            ->required()
                            ->default(now())
                            ->displayFormat('d/m/Y')
                            ->firstDayOfWeek(7)
                            ->closeOnDateSelection()
                            ->native(false),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TimePicker::make('check_in_time')
                                    ->label('وقت الحضور')
                                    ->required(fn (Forms\Get $get): bool => !$get('is_absent'))
                                    ->seconds(false)
                                    ->native(false)
                                    ->default('09:00')
                                    ->live()
                                    ->hidden(fn (Forms\Get $get): bool => $get('is_absent'))
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                        // Calculate late minutes when check_in_time changes
                                        $checkIn = $get('check_in_time');
                                        $date = $get('date');

                                        if ($checkIn && $date) {
                                            $attendanceDate = \Carbon\Carbon::parse($date);
                                            $checkInTime = \Carbon\Carbon::parse($checkIn);

                                            // Combine date and time
                                            $fullCheckIn = $attendanceDate->copy()->setTimeFrom($checkInTime);

                                            // Get expected start time based on date (09:00 for work days, Friday is day off)
                                            $expectedStartTime = $attendanceDate->dayOfWeek === 5
                                                ? \Carbon\Carbon::parse('00:00')
                                                : \Carbon\Carbon::parse('09:00');
                                            $fullExpectedStart = $attendanceDate->copy()->setTimeFrom($expectedStartTime);

                                            // Calculate late minutes (same logic as model's calculateLateMinutes)
                                            if ($fullCheckIn->gt($fullExpectedStart)) {
                                                $lateMinutes = $fullExpectedStart->diffInMinutes($fullCheckIn);
                                                $set('late_minutes_display', $lateMinutes);
                                                $set('late_minutes', $lateMinutes);
                                            } else {
                                                $set('late_minutes_display', 0);
                                                $set('late_minutes', 0);
                                            }
                                        }
                                        // Calculate working hours using same logic as model's calculateWorkingHours
                                        $checkIn = $get('check_in_time');
                                        $checkOut = $get('check_out_time');
                                        $date = $get('date');

                                        if ($checkIn && $checkOut && $date) {
                                            $attendanceDate = \Carbon\Carbon::parse($date);
                                            $checkInTime = \Carbon\Carbon::parse($checkIn);
                                            $checkOutTime = \Carbon\Carbon::parse($checkOut);

                                            // Combine date and times
                                            $fullCheckIn = $attendanceDate->copy()->setTimeFrom($checkInTime);
                                            $fullCheckOut = $attendanceDate->copy()->setTimeFrom($checkOutTime);

                                            // Calculate working hours (same logic as model)
                                            $workingHours = $fullCheckIn->diffInHours($fullCheckOut);
                                            $set('working_hours_display', number_format($workingHours, 2));
                                            $set('working_hours', number_format($workingHours, 2));

                                            // Calculate overtime (more than 8 hours, same logic as model)
                                            if ($workingHours > 8) {
                                                $overtimeHours = $workingHours - 8;
                                                $set('overtime_hours_display', number_format($overtimeHours, 2));
                                                $set('overtime_hours', number_format($overtimeHours, 2));
                                            } else {
                                                $set('overtime_hours_display', 0);
                                                $set('overtime_hours', 0);
                                            }
                                        }
                                    }),

                                Forms\Components\TimePicker::make('check_out_time')
                                    ->label('وقت الانصراف')
                                    ->required(fn (Forms\Get $get): bool => !$get('is_absent'))
                                    ->seconds(false)
                                    ->native(false)
                                    ->default('17:00')
                                    ->live()
                                    ->hidden(fn (Forms\Get $get): bool => $get('is_absent'))
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                        // Calculate working hours using same logic as model's calculateWorkingHours
                                        $checkIn = $get('check_in_time');
                                        $checkOut = $get('check_out_time');
                                        $date = $get('date');

                                        if ($checkIn && $checkOut && $date) {
                                            $attendanceDate = \Carbon\Carbon::parse($date);
                                            $checkInTime = \Carbon\Carbon::parse($checkIn);
                                            $checkOutTime = \Carbon\Carbon::parse($checkOut);

                                            // Combine date and times
                                            $fullCheckIn = $attendanceDate->copy()->setTimeFrom($checkInTime);
                                            $fullCheckOut = $attendanceDate->copy()->setTimeFrom($checkOutTime);

                                            // Calculate working hours (same logic as model)
                                            $workingHours = $fullCheckIn->diffInHours($fullCheckOut);
                                            $set('working_hours_display', number_format($workingHours, 2));
                                            $set('working_hours', number_format($workingHours, 2));

                                            // Calculate overtime (more than 8 hours, same logic as model)
                                            if ($workingHours > 8) {
                                                $overtimeHours = $workingHours - 8;
                                                $set('overtime_hours_display', number_format($overtimeHours, 2));
                                                $set('overtime_hours', number_format($overtimeHours, 2));
                                            } else {
                                                $set('overtime_hours_display', 0);
                                                $set('overtime_hours', 0);
                                            }
                                        }
                                    }),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('working_hours_display')
                                    ->label('ساعات العمل')
                                    ->numeric()
                                    ->step(0.01)
                                    ->disabled()
                                    ->default(8)
                                    ->dehydrated(false),

                                Forms\Components\TextInput::make('overtime_hours_display')
                                    ->label('العمل الإضافي (ساعات)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->disabled()
                                    ->default(0)
                                    ->dehydrated(false),

                                Forms\Components\TextInput::make('late_minutes_display')
                                    ->label('دقائق التأخير')
                                    ->numeric()
                                    ->disabled()
                                    ->default(0)
                                    ->dehydrated(false),
                            ]),

                        // Hidden fields to store values for saving
                        Forms\Components\Hidden::make('working_hours')
                            ->default(8),
                        Forms\Components\Hidden::make('overtime_hours')
                            ->default(0),
                        Forms\Components\Hidden::make('late_minutes')
                            ->default(0),

                        Forms\Components\Toggle::make('is_absent')
                            ->label('غائب')
                            ->default(false)
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                if ($state) {
                                    $set('check_in_time', '09:00');
                                    $set('check_out_time', '09:00');
                                    $set('working_hours_display', 0);
                                    $set('overtime_hours_display', 0);
                                    $set('late_minutes_display', 0);
                                    $set('working_hours', 0);
                                    $set('overtime_hours', 0);
                                    $set('late_minutes', 0);
                                }
                            }),

                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('التاريخ')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('employee.name_ar')
                    ->label('الموظف')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Attendance $record): string => $record->employee->name_en ?? ''),

                Tables\Columns\TextColumn::make('check_in_time')
                    ->label('وقت الحضور')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('check_out_time')
                    ->label('وقت الانصراف')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('working_hours')
                    ->label('ساعات العمل')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match(true) {
                        $state >= 8 => 'success',
                        $state >= 4 => 'warning',
                        default => 'danger',
                    }),

                Tables\Columns\TextColumn::make('overtime_hours')
                    ->label('العمل الإضافي')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('late_minutes')
                    ->label('التأخير (دقائق)')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => $state > 0 ? 'danger' : 'success')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_absent')
                    ->label('غائب')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('ملاحظات')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department')
                    ->label('القسم')
                    ->relationship('employee.department', 'name_ar')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('employee')
                    ->label('الموظف')
                    ->relationship('employee', 'name_ar')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('date_range')
                    ->label('نطاق التاريخ')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('من')
                            ->displayFormat('d/m/Y')
                            ->firstDayOfWeek(7)
                            ->native(false),
                        Forms\Components\DatePicker::make('until')
                            ->label('إلى')
                            ->displayFormat('d/m/Y')
                            ->firstDayOfWeek(7)
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),

                Tables\Filters\TernaryFilter::make('is_absent')
                    ->label('حالة الغياب')
                    ->placeholder('الكل')
                    ->trueLabel('غائب')
                    ->falseLabel('حاضر'),

                Tables\Filters\Filter::make('has_overtime')
                    ->label('لديه عمل إضافي')
                    ->query(fn (Builder $query): Builder => $query->where('overtime_hours', '>', 0)),

                Tables\Filters\Filter::make('is_late')
                    ->label('متأخر')
                    ->query(fn (Builder $query): Builder => $query->where('late_minutes', '>', 0)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('عرض'),
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
            'bulk' => Pages\BulkAttendance::route('/bulk'),
        ];
    }

    // public static function mutateFormDataBeforeCreate(array $data): array
    // {
    //     // Copy values from hidden fields to actual fields
    //     if (isset($data['working_hours'])) {
    //         $data['working_hours_display'] = $data['working_hours'];
    //     }
    //     if (isset($data['overtime_hours'])) {
    //         $data['overtime_hours_display'] = $data['overtime_hours'];
    //     }
    //     if (isset($data['late_minutes'])) {
    //         $data['late_minutes_display'] = $data['late_minutes'];
    //     }

    //     // Remove hidden fields
    //     unset($data['working_hours'], $data['overtime_hours'], $data['late_minutes']);

    //     return $data;
    // }

    // public static function mutateFormDataBeforeUpdate(array $data): array
    // {
    //     // Copy values from hidden fields to actual fields
    //     if (isset($data['working_hours'])) {
    //         $data['working_hours_display'] = $data['working_hours'];
    //     }
    //     if (isset($data['overtime_hours'])) {
    //         $data['overtime_hours_display'] = $data['overtime_hours'];
    //     }
    //     if (isset($data['late_minutes'])) {
    //         $data['late_minutes_display'] = $data['late_minutes'];
    //     }

    //     // Remove hidden fields
    //     unset($data['working_hours'], $data['overtime_hours'], $data['late_minutes']);

    //     return $data;
    // }
}
