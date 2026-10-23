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
                /*
                 * Temporary form-only fields.
                 *
                 * These are used during employee creation
                 * and are handled by CreateEmployee.
                 */

                Forms\Components\Hidden::make('login_email'),

                Forms\Components\Hidden::make('login_password'),

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
                                fn (?Employee $record): string =>
                                    $record?->employee_number
                                        ?? __('vitahr.automatically_generated')
                            ),

                        Forms\Components\Select::make('workLocations')
                            ->label(__('vitahr.work_locations'))
                            ->multiple()
                            ->relationship(
                                name: 'workLocations',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (
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
                        Forms\Components\Placeholder::make(
                            'login_account_status'
                        )
                            ->label(__('vitahr.account'))
                            ->content(function (Get $get): string {
                                $email = $get('login_email');

                                if ($email) {
                                    return $email;
                                }

                                return __('vitahr.no_login_account_configured');
                            }),

                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make(
                                'configureLoginAccount'
                            )
                                ->label(
                                    fn (?Employee $record): string =>
                                        $record?->user
                                            ? __('vitahr.edit_login_account')
                                            : __('vitahr.create_login_account')
                                )
                                ->icon('heroicon-o-key')
                                ->color('primary')
                                ->modalHeading(
                                    fn (?Employee $record): string =>
                                        $record?->user
                                            ? __('vitahr.edit_employee_login_account')
                                            : __('vitahr.create_employee_login_account')
                                )
                                ->modalDescription(
                                    fn (?Employee $record): string =>
                                        $record?->user
                                            ? __('vitahr.update_login_account_description')
                                            : __('vitahr.create_login_account_description')
                                )
                                ->modalSubmitActionLabel(
                                    __('vitahr.save_login_account')
                                )
                                ->form([
                                    Forms\Components\TextInput::make('email')
                                        ->label(__('vitahr.email'))
                                        ->email()
                                        ->required()
                                        ->maxLength(255)
                                        ->rule(
                                            fn (?Employee $record) =>
                                                \Illuminate\Validation\Rule::unique(
                                                    'users',
                                                    'email'
                                                )->ignore(
                                                    $record?->user?->id
                                                )
                                        ),

                                    Forms\Components\TextInput::make('password')
                                        ->label(__('vitahr.new_password'))
                                        ->password()
                                        ->revealable()
                                        ->minLength(8)
                                        ->nullable(),

                                    Forms\Components\TextInput::make(
                                        'password_confirmation'
                                    )
                                        ->label(
                                            __('vitahr.confirm_new_password')
                                        )
                                        ->password()
                                        ->revealable()
                                        ->same('password')
                                        ->requiredWith('password'),
                                ])
                                ->fillForm(
                                    fn (?Employee $record): array => [
                                        'email' => $record?->user?->email,
                                    ]
                                )
                                ->action(
                                    function (
                                        ?Employee $record,
                                        array $data,
                                        Set $set
                                    ): void {
                                        /*
                                         * Existing employee:
                                         * update the existing User account.
                                         */
                                        if ($record?->user) {
                                            $user = $record->user;

                                            $user->email = $data['email'];

                                            if (!empty($data['password'])) {
                                                $user->password =
                                                    \Illuminate\Support\Facades\Hash::make(
                                                        $data['password']
                                                    );
                                            }

                                            $user->save();

                                            $set(
                                                'login_email',
                                                $user->email
                                            );

                                            return;
                                        }

                                        /*
                                         * New employee:
                                         * keep login data in the main form state.
                                         *
                                         * CreateEmployee will create the User
                                         * when the employee is finally saved.
                                         */
                                        $set(
                                            'login_email',
                                            $data['email']
                                        );

                                        $set(
                                            'login_password',
                                            $data['password']
                                        );
                                    }
                                ),
                        ]),
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
                Tables\Actions\Action::make('editLoginAccount')
                    ->label(__('vitahr.login_account'))
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->modalHeading(
                        __('vitahr.edit_login_account')
                    )
                    ->modalDescription(
                        __('vitahr.update_login_account_description')
                    )
                    ->modalSubmitActionLabel(
                        __('vitahr.save_login_account')
                    )
                    ->form([
                        Forms\Components\TextInput::make('email')
                            ->label(__('vitahr.email'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->rule(
                                fn (?Employee $record) =>
                                    \Illuminate\Validation\Rule::unique(
                                        'users',
                                        'email'
                                    )->ignore(
                                        $record?->user?->id
                                    )
                            ),

                        Forms\Components\TextInput::make('password')
                            ->label(__('vitahr.new_password'))
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->nullable(),

                        Forms\Components\TextInput::make(
                            'password_confirmation'
                        )
                            ->label(
                                __('vitahr.confirm_new_password')
                            )
                            ->password()
                            ->revealable()
                            ->same('password')
                            ->requiredWith('password'),
                    ])
                    ->fillForm(
                        fn (Employee $record): array => [
                            'email' => $record->user?->email,
                        ]
                    )
                    ->action(
                        function (
                            Employee $record,
                            array $data
                        ): void {
                            $user = $record->user;

                            if (!$user) {
                                throw new \RuntimeException(
                                    __(
                                        'vitahr.no_login_account_error'
                                    )
                                );
                            }

                            $user->email = $data['email'];

                            if (!empty($data['password'])) {
                                $user->password =
                                    \Illuminate\Support\Facades\Hash::make(
                                        $data['password']
                                    );
                            }

                            $user->save();
                        }
                    ),

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
