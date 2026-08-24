<?php

namespace App\Filament\Resources;

use App\Filament\Actions\ExportBlankContractToPdfAction;
use App\Filament\Actions\ExportEmployeeAttendanceToPdfAction;
use App\Filament\Resources\EmployeeContractNewResource;
use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'الموظفين';

    protected static ?string $modelLabel = 'موظف';

    protected static ?string $pluralModelLabel = 'الموظفين';

    protected static ?string $navigationGroup = 'إدارة الموارد البشرية';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    // Step 1: Personal Information
                    Forms\Components\Wizard\Step::make('المعلومات الشخصية')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Forms\Components\Section::make('معلومات أساسية')
                                ->schema([
                                    Forms\Components\TextInput::make('name_ar')
                                        ->label('الاسم الكامل (عربي)')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('name_en')
                                        ->label('الاسم الكامل (إنكليزي)')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\Select::make('nationality')
                                        ->label('الجنسية')
                                        ->options([
                                            'syrian' => 'سوري',
                                            'other' => 'أخرى',
                                        ])
                                        ->required(),
                                    Forms\Components\DatePicker::make('date_of_birth')
                                        ->label('تاريخ الميلاد')
                                        ->required(),
                                    Forms\Components\Select::make('gender')
                                        ->label('الجنس')
                                        ->options([
                                            'male' => 'ذكر',
                                            'female' => 'أنثى',
                                        ])
                                        ->required(),
                                    Forms\Components\Select::make('marital_status')
                                        ->label('الحالة الاجتماعية')
                                        ->options([
                                            'single' => 'عازب',
                                            'married' => 'متزوج',
                                            'divorced' => 'مطلق',
                                            'widowed' => 'أرمل',
                                        ])
                                        ->required(),
                                    Forms\Components\TextInput::make('national_id')
                                        ->label('الرقم الوطني')
                                        ->required()
                                        ->maxLength(255)
                                        ->unique(ignoreRecord: true),
                                    Forms\Components\TextInput::make('place_of_registration')
                                        ->label('مكان القيد')
                                        ->maxLength(255),
                                    Forms\Components\FileUpload::make('photo')
                                        ->label('صورة شخصية')
                                        ->directory('employee-photos')
                                        ->maxSize(10240)
                                        ->nullable(),
                                ])
                                ->columns(2),
                        ]),

                    // Step 2: Contact Information
                    Forms\Components\Wizard\Step::make('معلومات التواصل')
                        ->icon('heroicon-o-phone')
                        ->schema([
                            Forms\Components\Section::make('بيانات الاتصال')
                                ->schema([
                                    Forms\Components\TextInput::make('phone')
                                        ->label('رقم الهاتف')
                                        ->tel()
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('email')
                                        ->label('البريد الإلكتروني')
                                        ->email()
                                        ->maxLength(255),
                                    Forms\Components\Textarea::make('current_address')
                                        ->label('العنوان الحالي')
                                        ->rows(3),
                                    Forms\Components\TextInput::make('emergency_contact_name')
                                        ->label('شخص للطوارئ')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('emergency_contact_phone')
                                        ->label('رقم هاتف الطوارئ')
                                        ->tel()
                                        ->maxLength(255),
                                ])
                                ->columns(2),
                        ]),

                    // Step 3: Work Information
                    Forms\Components\Wizard\Step::make('معلومات العمل')
                        ->icon('heroicon-o-briefcase')
                        ->schema([
                            Forms\Components\Section::make('البيانات الوظيفية')
                                ->schema([
                                    Forms\Components\Select::make('department_id')
                                        ->label('القسم')
                                        ->relationship('department', 'name_ar')
                                        ->searchable()
                                        ->preload()
                                        ->nullable(),
                                    Forms\Components\Select::make('job_title_id')
                                        ->label('المسمى الوظيفي')
                                        ->relationship('jobTitle', 'name_ar')
                                        ->searchable()
                                        ->preload()
                                        ->nullable(),
                                    Forms\Components\Select::make('direct_manager_id')
                                        ->label('المدير المباشر')
                                        ->relationship('directManager', 'name_ar')
                                        ->searchable()
                                        ->preload()
                                        ->nullable(),
                                    Forms\Components\Select::make('project_id')
                                        ->label('المشروع المرتبط')
                                        ->relationship('project', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->nullable(),
                                    Forms\Components\Select::make('contract_type')
                                        ->label('نوع العقد')
                                        ->options([
                                            'full_time' => 'دوام كامل',
                                            'part_time' => 'دوام جزئي',
                                            'daily' => 'يومي',
                                            'contractor' => 'مقاول',
                                        ])
                                        ->required(),
                                    Forms\Components\Select::make('employment_status')
                                        ->label('حالة الموظف')
                                        ->options([
                                            'active' => 'نشط',
                                            'suspended' => 'موقوف',
                                            'terminated' => 'منتهي',
                                        ])
                                        ->required()
                                        ->default('active'),
                                    Forms\Components\DatePicker::make('hire_date')
                                        ->label('تاريخ التوظيف')
                                        ->required(),
                                    Forms\Components\TextInput::make('probation_period')
                                        ->label('فترة التجربة (أيام)')
                                        ->required()
                                        ->numeric()
                                        ->default(90),
                                    Forms\Components\DatePicker::make('contract_start_date')
                                        ->label('تاريخ بداية العقد'),
                                    Forms\Components\DatePicker::make('contract_end_date')
                                        ->label('تاريخ نهاية العقد'),
                                ])
                                ->columns(2),
                        ]),

                    // Step 4: Salary Information
                    Forms\Components\Wizard\Step::make('الراتب والبدلات')
                        ->icon('heroicon-o-currency-dollar')
                        ->schema([
                            Forms\Components\Section::make('بيانات الراتب')
                                ->schema([
                                    Forms\Components\TextInput::make('basic_salary')
                                        ->label('الراتب الأساسي')
                                        ->required()
                                        ->numeric()
                                        ->prefix('SYP')
                                        ->default(0.00)
                                        ->live()
                                        ->reactive()
                                        ->afterStateUpdated(function (Forms\Set $set, $state, Forms\Get $get) {
                                            $housing = (float) $get('housing_allowance') ?? 0;
                                            $transport = (float) $get('transportation_allowance') ?? 0;
                                            $other = (float) $get('other_allowances') ?? 0;
                                            $set('total_salary', (float) $state + $housing + $transport + $other);
                                            $set('total_salary_text', (float) $state + $housing + $transport + $other);
                                        }),
                                    Forms\Components\TextInput::make('housing_allowance')
                                        ->label('بدل السكن')
                                        ->required()
                                        ->numeric()
                                        ->prefix('SYP')
                                        ->default(0.00)
                                        ->live()
                                        ->reactive()
                                        ->afterStateUpdated(function (Forms\Set $set, $state, Forms\Get $get) {
                                            $basic = (float) $get('basic_salary') ?? 0;
                                            $transport = (float) $get('transportation_allowance') ?? 0;
                                            $other = (float) $get('other_allowances') ?? 0;
                                            $set('total_salary', $basic + (float) $state + $transport + $other);
                                            $set('total_salary_text', $basic + (float) $state + $transport + $other);
                                        }),
                                    Forms\Components\TextInput::make('transportation_allowance')
                                        ->label('بدل المواصلات')
                                        ->required()
                                        ->numeric()
                                        ->prefix('SYP')
                                        ->default(0.00)
                                        ->live()
                                        ->reactive()
                                        ->afterStateUpdated(function (Forms\Set $set, $state, Forms\Get $get) {
                                            $basic = (float) $get('basic_salary') ?? 0;
                                            $housing = (float) $get('housing_allowance') ?? 0;
                                            $other = (float) $get('other_allowances') ?? 0;
                                            $set('total_salary', $basic + $housing + (float) $state + $other);
                                            $set('total_salary_text', $basic + $housing + (float) $state + $other);
                                        }),
                                    Forms\Components\TextInput::make('other_allowances')
                                        ->label('بدلات أخرى')
                                        ->required()
                                        ->numeric()
                                        ->prefix('SYP')
                                        ->default(0.00)
                                        ->live()
                                        ->reactive()
                                        ->afterStateUpdated(function (Forms\Set $set, $state, Forms\Get $get) {
                                            $basic = (float) $get('basic_salary') ?? 0;
                                            $housing = (float) $get('housing_allowance') ?? 0;
                                            $transport = (float) $get('transportation_allowance') ?? 0;
                                            $set('total_salary', $basic + $housing + $transport + (float) $state);
                                            $set('total_salary_text', $basic + $housing + $transport + (float) $state);
                                        }),
                                    Forms\Components\TextInput::make('total_salary_text')
                                        ->label('إجمالي الراتب')
                                        ->required()
                                        ->numeric()
                                        ->prefix('SYP')
                                        ->default(0)
                                        ->disabled(),
                                    Forms\Components\Hidden::make('total_salary'),
                                    Forms\Components\Select::make('payment_method')
                                        ->label('طريقة الدفع')
                                        ->options([
                                            'cash' => 'نقدي',
                                            'bank_transfer' => 'تحويل بنكي',
                                        ])
                                        ->required()
                                        ->default('cash'),
                                    Forms\Components\TextInput::make('bank_account_number')
                                        ->label('رقم الحساب البنكي')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('bank_name')
                                        ->label('اسم البنك')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('social_security_number')
                                        ->label('رقم الضمان الاجتماعي')
                                        ->maxLength(255),
                                ])
                                ->columns(2),
                        ]),

                    // Step 5: Notes
                    Forms\Components\Wizard\Step::make('ملاحظات')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Forms\Components\Textarea::make('notes')
                                ->label('ملاحظات إضافية')
                                ->rows(5)
                                ->columnSpanFull(),
                        ]),
                ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('')
                    ->circular()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('employee_number')
                    ->label('رقم الموظف')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name_ar')
                    ->label('الاسم (عربي)')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name_en')
                    ->label('الاسم (إنكليزي)')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('department.name_ar')
                    ->label('القسم')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('jobTitle.name_ar')
                    ->label('المسمى الوظيفي')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->label('المشروع')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('employment_status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'suspended' => 'warning',
                        'terminated' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'نشط',
                        'suspended' => 'موقوف',
                        'terminated' => 'منتهي',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_salary')
                    ->label('الراتب الإجمالي')
                    ->money('SYP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('hire_date')
                    ->label('تاريخ التوظيف')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('contract_end_date')
                    ->label('نهاية العقد')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                // Tables\Filters\SelectFilter::make('department')
                //     ->label('القسم')
                //     ->options(fn () => Employee::pluck('department', 'department')->unique()),
                Tables\Filters\SelectFilter::make('employment_status')
                    ->label('الحالة')
                    ->options([
                        'active' => 'نشط',
                        'suspended' => 'موقوف',
                        'terminated' => 'منتهي',
                    ]),
                Tables\Filters\SelectFilter::make('contract_type')
                    ->label('نوع العقد')
                    ->options([
                        'full_time' => 'دوام كامل',
                        'part_time' => 'دوام جزئي',
                        'daily' => 'يومي',
                        'contractor' => 'مقاول',
                    ]),
                Tables\Filters\Filter::make('contract_expiring_soon')
                    ->label('عقود تنتهي قريباً')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('contract_end_date', [now(), now()->addDays(30)])),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('create_contract')
                        ->label('إنشاء عقد')
                        ->icon('heroicon-o-document')
                        ->color('success')
                        ->url(fn ($record) => EmployeeContractNewResource::getUrl('create', ['employee_id' => $record->id]))
                        ->openUrlInNewTab()
                        ->hidden(fn ($record) => $record->contracts()->exists()),
                    Tables\Actions\Action::make('view_contracts')
                        ->label('عرض العقود')
                        ->icon('heroicon-o-folder-open')
                        ->color('info')
                        ->url(fn ($record) => EmployeeContractNewResource::getUrl('index'))
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('record_attendance')
                        ->label('تسجيل حضور')
                        ->icon('heroicon-o-clock')
                        ->color('primary')
                        ->url(fn ($record) => AttendanceResource::getUrl('bulk', ['employee_id' => $record->id])),
                    ExportEmployeeAttendanceToPdfAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->label('الإجراءات')
                ->icon('heroicon-o-ellipsis-horizontal')
                ->color('primary'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // Relation managers will be added in next phase
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
