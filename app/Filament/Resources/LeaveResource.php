<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveResource\Pages;
use App\Models\Leave;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeaveResource extends Resource
{
    protected static ?string $model = Leave::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'الإجازات';

    protected static ?string $modelLabel = 'إجازة';

    protected static ?string $pluralModelLabel = 'الإجازات';

    protected static ?string $navigationGroup = 'إدارة الموارد البشرية';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الإجازة')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label('الموظف')
                            ->relationship('employee', 'name_ar')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('leave_type')
                            ->label('نوع الإجازة')
                            ->required()
                            ->options([
                                'annual' => 'إجازة سنوية',
                                'sick' => 'إجازة مرضية',
                                'emergency' => 'إجازة طارئة',
                                'unpaid' => 'إجازة بدون راتب',
                            ])
                            ->live(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('تاريخ البداية')
                                    ->required()
                                    ->displayFormat('d/m/Y')
                                    ->firstDayOfWeek(7)
                                    ->closeOnDateSelection()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                        self::calculateTotalDays($set, $get);
                                    }),

                                Forms\Components\DatePicker::make('end_date')
                                    ->label('تاريخ النهاية')
                                    ->required()
                                    ->displayFormat('d/m/Y')
                                    ->firstDayOfWeek(7)
                                    ->closeOnDateSelection()
                                    ->native(false)
                                    ->live()
                                    ->after('start_date')
                                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                        self::calculateTotalDays($set, $get);
                                    }),
                            ]),

                        Forms\Components\TextInput::make('total_days')
                            ->label('عدد الأيام')
                            ->numeric()
                            ->disabled()
                            ->default(1),

                        Forms\Components\Textarea::make('reason')
                            ->label('السبب')
                            ->required()
                            ->rows(3)
                            ->placeholder('اكتب سبب الإجازة هنا'),

                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(2)
                            ->placeholder('أي ملاحظات إضافية'),
                    ])
                    ->columns(2),
            ]);
    }

    private static function calculateTotalDays(Forms\Set $set, Forms\Get $get): void
    {
        $startDate = $get('start_date');
        $endDate = $get('end_date');

        if ($startDate && $endDate) {
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);
            $totalDays = $start->diffInDays($end) + 1;
            $set('total_days', $totalDays);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name_ar')
                    ->label('الموظف')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Leave $record): string => $record->employee->name_en ?? ''),

                Tables\Columns\TextColumn::make('leave_type')
                    ->label('نوع الإجازة')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'annual' => 'success',
                        'sick' => 'warning',
                        'emergency' => 'danger',
                        'unpaid' => 'gray',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'annual' => 'إجازة سنوية',
                        'sick' => 'إجازة مرضية',
                        'emergency' => 'إجازة طارئة',
                        'unpaid' => 'بدون راتب',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('تاريخ البداية')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('تاريخ النهاية')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_days')
                    ->label('عدد الأيام')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('reason')
                    ->label('السبب')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الطلب')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employee')
                    ->label('الموظف')
                    ->relationship('employee', 'name_ar')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('leave_type')
                    ->label('نوع الإجازة')
                    ->options([
                        'annual' => 'إجازة سنوية',
                        'sick' => 'إجازة مرضية',
                        'emergency' => 'إجازة طارئة',
                        'unpaid' => 'بدون راتب',
                    ]),


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
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('end_date', '<=', $date),
                            );
                    }),
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
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListLeaves::route('/'),
            'create' => Pages\CreateLeave::route('/create'),
            'edit' => Pages\EditLeave::route('/{record}/edit'),
        ];
    }
}
