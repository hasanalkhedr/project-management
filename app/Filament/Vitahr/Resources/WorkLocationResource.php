<?php

namespace App\Filament\Vitahr\Resources;

use App\Filament\Vitahr\Resources\WorkLocationResource\Pages;
use App\Models\Vitahr\WorkLocation;
use DateTime;
use DateTimeZone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WorkLocationResource extends Resource
{
    protected static ?string $model = WorkLocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    public static function getNavigationLabel(): string
    {
        return __('vitahr.work_locations');
    }

    public static function getModelLabel(): string
    {
        return __('vitahr.work_location');
    }

    public static function getPluralModelLabel(): string
    {
        return __('vitahr.work_locations');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(
                    __('vitahr.location_information')
                )
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('vitahr.name'))
                            ->required()
                            ->maxLength(255),

                        // Forms\Components\Hidden::make('latitude')
                        //     ->required(),

                        // Forms\Components\Hidden::make('longitude')
                        //     ->required(),
                        Forms\Components\TextInput::make('latitude')
                            ->label(__('vitahr.latitude'))
                            ->required()
                            ->numeric()
                            ->minValue(-90)
                            ->maxValue(90)
                            ->step(0.0000001)
                            ->live(debounce: 500)
                            ->afterStateUpdated(function ($state, $livewire) {
                                $livewire->dispatch(
                                    'work-location-coordinates-updated',
                                    latitude: $state,
                                    longitude: $livewire->data['longitude'] ?? null,
                                );
                            }),

                        Forms\Components\TextInput::make('longitude')
                            ->label(__('vitahr.longitude'))
                            ->required()
                            ->numeric()
                            ->minValue(-180)
                            ->maxValue(180)
                            ->step(0.0000001)
                            ->live(debounce: 500)
                            ->afterStateUpdated(function ($state, $livewire) {
                                $livewire->dispatch(
                                    'work-location-coordinates-updated',
                                    latitude: $livewire->data['latitude'] ?? null,
                                    longitude: $state,
                                );
                            }),

                        Forms\Components\ViewField::make('location_map')
                            ->label(__('vitahr.location_on_map'))
                            ->view('filament.vitahr.forms.location-map')
                            ->viewData(
                                fn(Get $get): array => [
                                    'latitude' => $get('latitude'),
                                    'longitude' => $get('longitude'),
                                ]
                            )
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('radius')
                            ->label(__('vitahr.radius_meters'))
                            ->required()
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->default(100),

                        Forms\Components\Select::make('timezone')
                            ->label(__('vitahr.timezone'))
                            ->required()
                            ->searchable()
                            ->options(
                                self::getTimezoneOptions()
                            ),

                        Forms\Components\TimePicker::make(
                            'work_start_time'
                        )
                            ->label(__('vitahr.work_start_time'))
                            ->required()
                            ->seconds(false)
                            ->native(false),

                        Forms\Components\TimePicker::make(
                            'work_end_time'
                        )
                            ->label(__('vitahr.work_end_time'))
                            ->required()
                            ->seconds(false)
                            ->native(false),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('vitahr.active'))
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * Generate timezone options such as:
     *
     * America/New_York — UTC-04:00
     * Europe/Berlin — UTC+02:00
     * Asia/Dubai — UTC+04:00
     */
    protected static function getTimezoneOptions(): array
    {
        $timezones = DateTimeZone::listIdentifiers();

        $options = [];

        foreach ($timezones as $timezone) {
            try {
                $dateTime = new DateTime(
                    'now',
                    new DateTimeZone($timezone)
                );

                $offsetSeconds = $dateTime->getOffset();

                $sign = $offsetSeconds >= 0 ? '+' : '-';

                $absoluteOffset = abs($offsetSeconds);

                $hours = intdiv(
                    $absoluteOffset,
                    3600
                );

                $minutes = intdiv(
                    $absoluteOffset % 3600,
                    60
                );

                $offset = sprintf(
                    'UTC%s%02d:%02d',
                    $sign,
                    $hours,
                    $minutes
                );

                $options[$timezone] =
                    "{$timezone} — {$offset}";
            } catch (\Throwable) {
                continue;
            }
        }

        return $options;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('vitahr.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('latitude')
                    ->label(__('vitahr.latitude'))
                    ->numeric(
                        decimalPlaces: 7
                    )
                    ->toggleable(),

                Tables\Columns\TextColumn::make('longitude')
                    ->label(__('vitahr.longitude'))
                    ->numeric(
                        decimalPlaces: 7
                    )
                    ->toggleable(),

                Tables\Columns\TextColumn::make('radius')
                    ->label(__('vitahr.radius'))
                    ->suffix(' m')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('timezone')
                    ->label(__('vitahr.timezone'))
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make(
                    'work_start_time'
                )
                    ->label(__('vitahr.start'))
                    ->time('H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'work_end_time'
                )
                    ->label(__('vitahr.end'))
                    ->time('H:i')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('vitahr.active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'employees_count'
                )
                    ->label(__('vitahr.employees'))
                    ->counts('employees')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('vitahr.created'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('vitahr.updated'))
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

                Tables\Filters\SelectFilter::make('timezone')
                    ->label(__('vitahr.timezone'))
                    ->options(
                        self::getTimezoneOptions()
                    )
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort(
                'created_at',
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
            'index' => Pages\ListWorkLocations::route('/'),
            'create' => Pages\CreateWorkLocation::route('/create'),
            'edit' => Pages\EditWorkLocation::route('/{record}/edit'),
        ];
    }
}
