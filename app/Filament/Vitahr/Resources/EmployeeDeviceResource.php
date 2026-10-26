<?php

namespace App\Filament\Vitahr\Resources;

use App\Filament\Vitahr\Resources\EmployeeDeviceResource\Pages;
use App\Models\Vitahr\EmployeeDevice;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeDeviceResource extends Resource
{
    protected static ?string $model = EmployeeDevice::class;

<<<<<<< HEAD
    protected static ?string $navigationGroup = 'نظام الحضور والتصراف';
=======
>>>>>>> 86eefc5d13472f7be23f475d108a730aa12de8b0
    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('vitahr.employee_devices');
    }

    public static function getModelLabel(): string
    {
        return __('vitahr.employee_device');
    }

    public static function getPluralModelLabel(): string
    {
        return __('vitahr.employee_devices');
    }

    /*
     * Devices are registered from the mobile application
     * through the API.
     *
     * Filament is used for management only.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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

                Tables\Columns\TextColumn::make(
                    'device_identifier'
                )
                    ->label(__('vitahr.device_identifier'))
                    ->searchable()
                    ->copyable()
                    ->copyMessage(
                        __('vitahr.device_identifier_copied')
                    )
                    ->copyMessageDuration(1500)
                    ->formatStateUsing(
                        fn (?string $state): string =>
                            $state
                                ? static::shortenDeviceIdentifier($state)
                                : '-'
                    )
                    ->tooltip(
                        fn (?EmployeeDevice $record): ?string =>
                            $record?->device_identifier
                    ),

                Tables\Columns\TextColumn::make(
                    'device_name'
                )
                    ->label(__('vitahr.device'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('platform')
                    ->label(__('vitahr.platform'))
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string =>
                            $state
                                ? ucfirst($state)
                                : '-'
                    )
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('vitahr.status'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'registered_at'
                )
                    ->label(__('vitahr.registered'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'last_used_at'
                )
                    ->label(__('vitahr.last_used'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->placeholder(__('vitahr.never')),

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
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('vitahr.status'))
                    ->placeholder(__('vitahr.all'))
                    ->trueLabel(__('vitahr.active'))
                    ->falseLabel(__('vitahr.inactive')),

                Tables\Filters\SelectFilter::make('platform')
                    ->label(__('vitahr.platform'))
                    ->options([
                        'android' => __('vitahr.android'),
                        'ios' => __('vitahr.ios'),
                    ]),

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
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('vitahr.view'))
                    ->icon('heroicon-o-eye')
                    ->modalHeading(
                        __('vitahr.device_details')
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
                                fn (
                                    EmployeeDevice $record
                                ): string =>
                                    $record->employee?->employee_number
                                    . ' — '
                                    . (
                                        $record->employee?->user?->name
                                        ?? '-'
                                    )
                            ),

                        Forms\Components\Placeholder::make(
                            'device_identifier'
                        )
                            ->label(
                                __('vitahr.device_identifier')
                            )
                            ->content(
                                fn (
                                    EmployeeDevice $record
                                ): string =>
                                    $record->device_identifier
                            ),

                        Forms\Components\Placeholder::make(
                            'device_name'
                        )
                            ->label(
                                __('vitahr.device_name')
                            )
                            ->content(
                                fn (
                                    EmployeeDevice $record
                                ): string =>
                                    $record->device_name ?? '-'
                            ),

                        Forms\Components\Placeholder::make(
                            'platform'
                        )
                            ->label(__('vitahr.platform'))
                            ->content(
                                fn (
                                    EmployeeDevice $record
                                ): string =>
                                    ucfirst($record->platform)
                            ),

                        Forms\Components\Placeholder::make(
                            'status'
                        )
                            ->label(__('vitahr.status'))
                            ->content(
                                fn (
                                    EmployeeDevice $record
                                ): string =>
                                    $record->is_active
                                        ? __('vitahr.active')
                                        : __('vitahr.inactive')
                            ),

                        Forms\Components\Placeholder::make(
                            'registered_at'
                        )
                            ->label(
                                __('vitahr.registered_at')
                            )
                            ->content(
                                fn (
                                    EmployeeDevice $record
                                ): string =>
                                    $record->registered_at
                                        ?->format('Y-m-d H:i:s')
                                    ?? '-'
                            ),

                        Forms\Components\Placeholder::make(
                            'last_used_at'
                        )
                            ->label(
                                __('vitahr.last_used_at')
                            )
                            ->content(
                                fn (
                                    EmployeeDevice $record
                                ): string =>
                                    $record->last_used_at
                                        ?->format('Y-m-d H:i:s')
                                    ?? __('vitahr.never')
                            ),
                    ]),
            ])
            ->bulkActions([])
            ->defaultSort(
                'created_at',
                'desc'
            );
    }

    protected static function shortenDeviceIdentifier(
        string $identifier
    ): string {
        $length = strlen($identifier);

        if ($length <= 16) {
            return $identifier;
        }

        return substr($identifier, 0, 8)
            . '...'
            . substr($identifier, -8);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeeDevices::route('/'),
        ];
    }
}
