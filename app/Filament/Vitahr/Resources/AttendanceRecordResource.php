<?php

namespace App\Filament\Vitahr\Resources;

use App\Filament\Vitahr\Resources\AttendanceRecordResource\Pages;
use App\Models\Vitahr\AttendanceRecord;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Vitahr\AttendanceEvent;

class AttendanceRecordResource extends Resource
{
    protected static ?string $model = AttendanceRecord::class;

    protected static ?string $navigationIcon =
        'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('vitahr.attendance_records');
    }

    public static function getModelLabel(): string
    {
        return __('vitahr.attendance_record');
    }

    public static function getPluralModelLabel(): string
    {
        return __('vitahr.attendance_records');
    }

    /*
     * Attendance records are created by the mobile API.
     *
     * They must not be created manually from Filament.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn($query) => $query->with([
                    'attendanceEvents',
                    'checkInWorkLocation',
                    'checkOutWorkLocation',
                ])
            )
            ->columns([
                /*
                 * Employee
                 */
                Tables\Columns\TextColumn::make(
                    'employee.employee_number'
                )
                    ->label(__('vitahr.employee_number'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'employee.user.name'
                )
                    ->label(__('vitahr.employee'))
                    ->searchable()
                    ->sortable(),

                /*
                 * Attendance source
                 */
                Tables\Columns\TextColumn::make('attendance_source')
                    ->label(__('vitahr.source'))
                    ->state(
                        fn(AttendanceRecord $record): string =>
                            $record->attendanceEvents->isNotEmpty()
                            ? __('vitahr.offline')
                            : __('vitahr.online')
                    )
                    ->badge()
                    ->icon(
                        fn(AttendanceRecord $record): string =>
                            $record->attendanceEvents->isNotEmpty()
                            ? 'heroicon-o-arrow-down-tray'
                            : 'heroicon-o-signal'
                    )
                    ->color(
                        fn(AttendanceRecord $record): string =>
                            $record->attendanceEvents->isNotEmpty()
                            ? 'warning'
                            : 'success'
                    ),

                /*
                 * Check-in
                 */
                Tables\Columns\TextColumn::make(
                    'check_in_at'
                )
                    ->label(__('vitahr.check_in'))
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),

                /*
                 * Check-out
                 */
                Tables\Columns\TextColumn::make(
                    'check_out_at'
                )
                    ->label(__('vitahr.check_out'))
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->placeholder(__('vitahr.not_checked_out')),

                /*
                 * Work location the check-in was validated at
                 */
                Tables\Columns\TextColumn::make(
                    'checkInWorkLocation.name'
                )
                    ->label(__('vitahr.work_location'))
                    ->placeholder('-'),

                /*
                 * Attendance status
                 */
                Tables\Columns\TextColumn::make('status')
                    ->label(__('vitahr.status'))
                    ->state(
                        fn(
                        AttendanceRecord $record
                    ): string =>
                            $record->check_out_at
                            ? __('vitahr.completed')
                            : __('vitahr.open')
                    )
                    ->badge()
                    ->color(
                        fn(string $state): string =>
                            match ($state) {
                                __('vitahr.completed') => 'success',
                                __('vitahr.open') => 'warning',
                                default => 'gray',
                            }
                    ),

                /*
                 * Check-in device
                 */
                Tables\Columns\TextColumn::make(
                    'checkInDevice.device_name'
                )
                    ->label(__('vitahr.check_in_device'))
                    ->placeholder('-')
                    ->searchable(),

                /*
                 * Check-out device
                 */
                Tables\Columns\TextColumn::make(
                    'checkOutDevice.device_name'
                )
                    ->label(__('vitahr.check_out_device'))
                    ->placeholder('-')
                    ->searchable(),

                /*
                 * Created
                 */
                Tables\Columns\TextColumn::make(
                    'created_at'
                )
                    ->label(__('vitahr.created'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),
            ])
            ->filters([
                /*
                 * Employee filter
                 */
                Tables\Filters\SelectFilter::make(
                    'employee_id'
                )
                    ->label(__('vitahr.employee'))
                    ->relationship(
                        'employee',
                        'employee_number'
                    )
                    ->searchable()
                    ->preload(),

                /*
                 * Check-in date range
                 */
                Tables\Filters\Filter::make('check_in_date')
                    ->label(__('vitahr.check_in_date'))
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label(__('vitahr.from')),

                        Forms\Components\DatePicker::make('until')
                            ->label(__('vitahr.until')),
                    ])
                    ->query(
                        function ($query, array $data) {
                            return $query
                                ->when(
                                    $data['from'] ?? null,
                                    fn($query, $date) =>
                                        $query->whereDate(
                                            'check_in_at',
                                            '>=',
                                            $date
                                        )
                                )
                                ->when(
                                    $data['until'] ?? null,
                                    fn($query, $date) =>
                                        $query->whereDate(
                                            'check_in_at',
                                            '<=',
                                            $date
                                        )
                                );
                        }
                    ),

                /*
                 * Attendance status
                 */
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('vitahr.status'))
                    ->options([
                        'open' => __('vitahr.open'),
                        'completed' => __('vitahr.completed'),
                    ])
                    ->query(
                        function ($query, array $data) {
                            return $query->when(
                                $data['value'] ?? null,
                                function ($query, $status) {
                                    if ($status === 'open') {
                                        $query->whereNull(
                                            'check_out_at'
                                        );
                                    }

                                    if ($status === 'completed') {
                                        $query->whereNotNull(
                                            'check_out_at'
                                        );
                                    }
                                }
                            );
                        }
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('vitahr.view'))
                    ->icon('heroicon-o-eye')
                    ->modalHeading(
                        __('vitahr.attendance_record')
                    )
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(
                        __('vitahr.close')
                    )
                    ->form([
                        Forms\Components\Placeholder::make(
                            'employee'
                        )
                            ->label(__('vitahr.employee'))
                            ->content(
                                fn(
                                AttendanceRecord $record
                            ): string =>
                                    $record->employee?->employee_number
                                    . ' — '
                                    . (
                                        $record->employee?->user?->name
                                        ?? '-'
                                    )
                            ),

                        Forms\Components\Placeholder::make('attendance_source')
                            ->label(__('vitahr.source'))
                            ->content(
                                fn(AttendanceRecord $record): string =>
                                    $record->attendanceEvents->isNotEmpty()
                                    ? __('vitahr.offline')
                                    : __('vitahr.online')
                            ),

                        Forms\Components\Placeholder::make(
                            'check_in_at'
                        )
                            ->label(__('vitahr.check_in_at'))
                            ->content(
                                fn(
                                AttendanceRecord $record
                            ): string =>
                                    $record->check_in_at
                                            ?->format('Y-m-d H:i:s')
                                    ?? '-'
                            ),

                        Forms\Components\Placeholder::make(
                            'check_in_location'
                        )
                            ->label(
                                __('vitahr.check_in_location')
                            )
                            ->content(
                                fn(
                                AttendanceRecord $record
                            ): string =>
                                    $record->check_in_latitude
                                    . ', '
                                    . $record->check_in_longitude
                            ),

                        Forms\Components\Placeholder::make(
                            'check_in_work_location'
                        )
                            ->label(
                                __('vitahr.check_in_work_location')
                            )
                            ->content(
                                fn(
                                AttendanceRecord $record
                            ): string =>
                                    $record
                                        ->checkInWorkLocation
                                            ?->name
                                    ?? '-'
                            ),

                        Forms\Components\Placeholder::make(
                            'check_in_accuracy'
                        )
                            ->label(
                                __('vitahr.check_in_accuracy')
                            )
                            ->content(
                                fn(
                                AttendanceRecord $record
                            ): string =>
                                    $record->check_in_accuracy !== null
                                    ? $record->check_in_accuracy . ' m'
                                    : '-'
                            ),

                        Forms\Components\Placeholder::make(
                            'check_in_device'
                        )
                            ->label(
                                __('vitahr.check_in_device')
                            )
                            ->content(
                                fn(
                                AttendanceRecord $record
                            ): string =>
                                    $record
                                        ->checkInDevice
                                            ?->device_name
                                    ?? $record
                                        ->checkInDevice
                                            ?->device_identifier
                                    ?? '-'
                            ),

                        Forms\Components\Placeholder::make(
                            'check_out_at'
                        )
                            ->label(__('vitahr.check_out_at'))
                            ->content(
                                fn(
                                AttendanceRecord $record
                            ): string =>
                                    $record->check_out_at
                                            ?->format('Y-m-d H:i:s')
                                    ?? __('vitahr.not_checked_out')
                            ),

                        Forms\Components\Placeholder::make(
                            'check_out_location'
                        )
                            ->label(
                                __('vitahr.check_out_location')
                            )
                            ->content(
                                fn(
                                AttendanceRecord $record
                            ): string =>
                                    $record->check_out_latitude !== null
                                    && $record->check_out_longitude !== null
                                    ? $record->check_out_latitude
                                    . ', '
                                    . $record->check_out_longitude
                                    : '-'
                            ),

                        Forms\Components\Placeholder::make(
                            'check_out_work_location'
                        )
                            ->label(
                                __('vitahr.check_out_work_location')
                            )
                            ->content(
                                fn(
                                AttendanceRecord $record
                            ): string =>
                                    $record
                                        ->checkOutWorkLocation
                                            ?->name
                                    ?? '-'
                            ),

                        Forms\Components\Placeholder::make(
                            'check_out_accuracy'
                        )
                            ->label(
                                __('vitahr.check_out_accuracy')
                            )
                            ->content(
                                fn(
                                AttendanceRecord $record
                            ): string =>
                                    $record->check_out_accuracy !== null
                                    ? $record->check_out_accuracy . ' m'
                                    : '-'
                            ),

                        Forms\Components\Placeholder::make(
                            'check_out_device'
                        )
                            ->label(
                                __('vitahr.check_out_device')
                            )
                            ->content(
                                fn(
                                AttendanceRecord $record
                            ): string =>
                                    $record
                                        ->checkOutDevice
                                            ?->device_name
                                    ?? $record
                                        ->checkOutDevice
                                            ?->device_identifier
                                    ?? '-'
                            ),

                        Forms\Components\Placeholder::make(
                            'duration'
                        )
                            ->label(__('vitahr.duration'))
                            ->content(
                                function (AttendanceRecord $record): string {
                                    if (
                                        !$record->check_in_at
                                        || !$record->check_out_at
                                    ) {
                                        return '-';
                                    }

                                    $minutes = $record
                                        ->check_in_at
                                        ->diffInMinutes(
                                            $record->check_out_at
                                        );

                                    $hours = intdiv(
                                        $minutes,
                                        60
                                    );

                                    $remainingMinutes =
                                        $minutes % 60;

                                    return sprintf(
                                        '%dh %02dm',
                                        $hours,
                                        $remainingMinutes
                                    );
                                }
                            ),
                    ]),
            ])
            ->bulkActions([])
            ->defaultSort(
                'check_in_at',
                'desc'
            );
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceRecords::route('/'),
        ];
    }
}
