<?php

namespace App\Filament\Vitahr\Resources;

use App\Filament\Vitahr\Resources\EmployeeResource\Pages;
use App\Models\Vitahr\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationGroup = 'نظام الحضور والتصراف';
    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('vitahr.employees');
    }

    public static function getModelLabel(): string
    {
        return __('vitahr.employee_name');
    }

    public static function getPluralModelLabel(): string
    {
        return __('vitahr.employees');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(
                    __('vitahr.employee_information')
                )
                    ->schema([
                        Forms\Components\TextInput::make('emp_name')
                            ->label(__('vitahr.employee_name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Placeholder::make(
                            'employee_number_display'
                        )
                            ->label(__('vitahr.employee_number'))
                            ->content(
                                fn(?Employee $record): string =>
                                    $record?->employee_number
                                    ?? __('vitahr.automatically_generated')
                            ),

                        Forms\Components\Select::make('workLocations')
                            ->label(__('vitahr.work_locations'))
                            ->multiple()
                            ->relationship(
                                name: 'workLocations',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(
                                Builder $query
                            ) => $query
                                        ->where('is_active', true)
                                        ->orderBy('name')
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('vitahr.active'))
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(
                    __('vitahr.login_account')
                )
                    ->description(
                        __('vitahr.login_account_description')
                    )
                    ->schema([
                        Forms\Components\Toggle::make('has_login_account')
                            ->label(__('vitahr.has_login_account'))
                            ->helperText(
                                __('vitahr.has_login_account_description')
                            )
                            ->default(false)
                            ->live()
                            ->afterStateHydrated(
                                function (Forms\Components\Toggle $component, ?Employee $record): void {
                                    /*
                                     * عند فتح صفحة التعديل:
                                     * فعّل الـ switch تلقائياً إذا كان
                                     * الموظف يملك حساب مستخدم.
                                     */
                                    if ($record?->user) {
                                        $component->state(true);
                                    }
                                }
                            )
                            ->afterStateUpdated(
                                function (bool $state, Set $set): void {
                                    /*
                                     * عند إطفاء الـ switch:
                                     * امسح بيانات الاعتماد من الفورم
                                     * حتى لا تُرسل مع الطلب.
                                     */
                                    if (!$state) {
                                        $set('login_email', null);
                                        $set('login_password', null);
                                        $set('password_confirmation', null);
                                    }
                                }
                            ),

                        Forms\Components\TextInput::make('login_email')
                            ->label(__('vitahr.email'))
                            ->email()
                            ->maxLength(255)
                            ->nullable()
                            ->visible(
                                fn(Get $get): bool =>
                                    (bool) $get('has_login_account')
                            )
                            ->required(
                                fn(Get $get): bool =>
                                    (bool) $get('has_login_account')
                            )
                            ->rule(
                                fn(?Employee $record) =>
                                    \Illuminate\Validation\Rule::unique(
                                        'users',
                                        'email'
                                    )->ignore($record?->user?->id)
                            ),

                        Forms\Components\TextInput::make('login_password')
                            ->label(
                                fn(?Employee $record): string =>
                                    $record?->user
                                    ? __('vitahr.new_password')
                                    : __('vitahr.password')
                            )
                            ->helperText(
                                fn(?Employee $record): ?string =>
                                    $record?->user
                                    ? __('vitahr.leave_blank_to_keep_password')
                                    : null
                            )
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->nullable()
                            ->visible(
                                fn(Get $get): bool =>
                                    (bool) $get('has_login_account')
                            )
                            ->required(
                                function (Get $get, ?Employee $record): bool {
                                    /*
                                     * في وضع الإنشاء: كلمة المرور مطلوبة
                                     * عند تفعيل الـ switch.
                                     * في وضع التعديل: ليست مطلوبة إذا كان
                                     * المستخدم موجوداً (لأنها اختيارية للتغيير).
                                     */
                                    if (!(bool) $get('has_login_account')) {
                                        return false;
                                    }

                                    return $record?->user === null;
                                }
                            ),

                        Forms\Components\TextInput::make(
                            'password_confirmation'
                        )
                            ->label(__('vitahr.confirm_password'))
                            ->password()
                            ->revealable()
                            ->same('login_password')
                            ->requiredWith('login_password')
                            ->visible(
                                fn(Get $get): bool =>
                                    (bool) $get('has_login_account')
                            ),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_number')
                    ->label(__('vitahr.employee_number'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('emp_name')
                    ->label(__('vitahr.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label(__('vitahr.email'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('workLocations.name')
                    ->label(__('vitahr.work_locations'))
                    ->badge()
                    ->separator(',')
                    ->searchable(),

                Tables\Columns\TextColumn::make('devices_count')
                    ->label(__('vitahr.devices'))
                    ->counts('devices')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('vitahr.active'))
                    ->boolean()
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

                Tables\Filters\SelectFilter::make('work_locations')
                    ->label(__('vitahr.work_locations'))
                    ->relationship(
                        'workLocations',
                        'name'
                    )
                    ->searchable()
                    ->preload(),
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
