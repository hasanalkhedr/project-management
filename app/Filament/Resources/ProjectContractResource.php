<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectContractResource\Pages;
use App\Filament\Resources\ProjectContractResource\RelationManagers;
use App\Models\ProjectContract;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Filament\Actions\ExportContractToPdfAction;
class ProjectContractResource extends Resource
{
    protected static ?string $model = ProjectContract::class;

    protected static ?string $navigationIcon = 'heroicon-s-document-text';

    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'عقود المشاريع';

    protected static ?string $modelLabel = 'عقد بناء';

    protected static ?string $pluralModelLabel = 'عقود المشاريع';

    //protected static ?string $navigationGroup = 'العقود';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // تبويب معلومات الطرفين
                Forms\Components\Tabs::make('العقد')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('الطرف الأول (المالك)')
                            ->schema([
                                Forms\Components\Section::make('معلومات المالك')
                                    ->description('معلومات الطرف الأول (صاحب المشروع)')
                                    ->schema([
                                        Forms\Components\TextInput::make('owner_name')
                                            ->label('اسم المالك')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('owner_id_number')
                                            ->label('رقم الهوية')
                                            ->required()
                                            ->maxLength(50),
                                        Forms\Components\Textarea::make('owner_address')
                                            ->label('عنوان المالك')
                                            ->required()
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('owner_phone')
                                            ->label('هاتف المالك')
                                            ->required()
                                            ->tel()
                                            ->maxLength(20),
                                    ])->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('الطرف الثاني (المقاول)')
                            ->schema([
                                Forms\Components\Section::make('معلومات المقاول')
                                    ->description('معلومات الطرف الثاني (الشركة المنفذة)')
                                    ->schema([
                                        Forms\Components\TextInput::make('contractor_name')
                                            ->label('اسم المقاول / الشركة')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('contractor_commercial_registration')
                                            ->label('السجل التجاري')
                                            ->required()
                                            ->maxLength(100),
                                        Forms\Components\Textarea::make('contractor_address')
                                            ->label('عنوان المقاول')
                                            ->required()
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('contractor_phone')
                                            ->label('هاتف المقاول')
                                            ->required()
                                            ->tel()
                                            ->maxLength(20),
                                    ])->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('معلومات المشروع')
                            ->schema([
                                Forms\Components\Section::make('تفاصيل المشروع')
                                    ->schema([
                                        Forms\Components\Textarea::make('project_location')
                                            ->label('موقع المشروع')
                                            ->required()
                                            ->columnSpanFull(),
                                        // Forms\Components\Textarea::make('contract_subject')
                                        //     ->label('موضوع العقد')
                                        //     ->required()
                                        //     ->columnSpanFull()
                                        //     ->helperText('وصف تفصيلي لأعمال البناء والتشطيب المطلوبة'),
                                    ]),

                                // Forms\Components\Section::make('المخططات والمواصفات')
                                //     ->schema([
                                //         Forms\Components\FileUpload::make('approved_drawings')
                                //             ->label('المخططات المعتمدة')
                                //             ->multiple()
                                //             ->directory('contracts/drawings')
                                //             ->preserveFilenames(),
                                //         Forms\Components\FileUpload::make('technical_specifications')
                                //             ->label('المواصفات الفنية')
                                //             ->multiple()
                                //             ->directory('contracts/specifications')
                                //             ->preserveFilenames(),
                                //     ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('المدة والجدول الزمني')
                            ->schema([
                                Forms\Components\Section::make('فترة التنفيذ')
                                    ->schema([
                                        Forms\Components\TextInput::make('execution_period')
                                            ->label('مدة التنفيذ (بالأيام)')
                                            ->required()
                                            ->numeric()
                                            ->minValue(1),
                                        // Forms\Components\DatePicker::make('start_date')
                                        //     ->label('تاريخ البدء')
                                        //     ->required()
                                        //     ->native(false),
                                        // Forms\Components\DatePicker::make('end_date')
                                        //     ->label('تاريخ الانتهاء المتوقع')
                                        //     ->required()
                                        //     ->native(false),
                                        // Forms\Components\DateTimePicker::make('site_delivery_date')
                                        //     ->label('تاريخ تسليم الموقع')
                                        //     ->native(false),
                                    ])->columns(2),

                                Forms\Components\Section::make('غرامات التأخير')
                                    ->schema([
                                        Forms\Components\TextInput::make('delay_penalty_percentage')
                                            ->label('نسبة غرامة التأخير اليومية (%)')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->step(0.1)
                                            ->suffix('%'),
                                        Forms\Components\TextInput::make('max_penalty_percentage')
                                            ->label('الحد الأقصى للغرامة (%)')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->step(0.1)
                                            ->suffix('%'),
                                    ])->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('القيمة والدفعات')
                            ->schema([
                                Forms\Components\Section::make('القيمة الإجمالية')
                                    ->schema([
                                        Forms\Components\Select::make('currency_id')
                                            ->label('العملة')
                                            ->required()
                                            ->relationship('currency', 'name')
                                            ->preload()
                                            ->searchable(),
                                        Forms\Components\TextInput::make('total_contract_value')
                                            ->label('القيمة الإجمالية للعقد')
                                            ->required()
                                            ->numeric()
                                            ->minValue(0)
                                            ->prefix('$'),
                                    ])->columns(2),

                                Forms\Components\Section::make('جدول الدفعات')
                                    ->description('نسب الدفعات المتفق عليها')
                                    ->schema([
                                        Forms\Components\TextInput::make('initial_payment_percentage')
                                            ->label('الدفعة الأولى عند التوقيع (%)')
                                            ->required()
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->step(0.1)
                                            ->suffix('%'),
                                        Forms\Components\TextInput::make('concrete_stage_payment_percentage')
                                            ->label('الدفعة بعد الهيكل الخرساني (%)')
                                            ->required()
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->step(0.1)
                                            ->suffix('%'),
                                        Forms\Components\TextInput::make('finishing_stage_payment_percentage')
                                            ->label('الدفعة بعد التشطيب (%)')
                                            ->required()
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->step(0.1)
                                            ->suffix('%'),
                                        Forms\Components\TextInput::make('final_payment_percentage')
                                            ->label('الدفعة النهائية (%)')
                                            ->required()
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->step(0.1)
                                            ->suffix('%'),
                                    ])->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('الضمان والأحكام')
                            ->schema([
                                // Forms\Components\Section::make('الضمان')
                                //     ->schema([
                                //         Forms\Components\TextInput::make('warranty_period')
                                //             ->label('فترة الضمان (بالأشهر)')
                                //             ->required()
                                //             ->numeric()
                                //             ->minValue(1)
                                //             ->default(12)
                                //             ->suffix('شهر'),
                                //     ]),

                                Forms\Components\Section::make('التحكيم')
                                    ->schema([
                                        Forms\Components\TextInput::make('arbitration_location')
                                            ->label('مكان التحكيم')
                                            ->required()
                                            ->maxLength(255),
                                    ]),

                                // Forms\Components\Section::make('الأحكام العامة')
                                //     ->schema([
                                //         Forms\Components\Textarea::make('general_terms')
                                //             ->label('الأحكام العامة')
                                //             ->columnSpanFull(),
                                //         Forms\Components\Textarea::make('notes')
                                //             ->label('ملاحظات إضافية')
                                //             ->columnSpanFull(),
                                //     ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('حالة العقد')
                            ->schema([
                                Forms\Components\Section::make('حالة العقد')
                                    ->schema([
                                        Forms\Components\Select::make('status')
                                            ->label('حالة العقد')
                                            ->required()
                                            ->options([
                                                'pending' => 'قيد الانتظار',
                                                'active' => 'نشط',
                                                'completed' => 'مكتمل',
                                                'terminated' => 'منتهي',
                                                'cancelled' => 'ملغي',
                                            ])
                                            ->default('pending'),
                                        Forms\Components\DatePicker::make('contract_date')
                                            ->label('تاريخ العقد')
                                            ->required()
                                            ->native(false),
                                    ])->columns(2),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contract_number')
                    ->label('رقم العقد')
                    ->default(fn($record) => 'CONTRACT-' . $record->id)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('owner_name')
                    ->label('اسم المالك')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('contractor_name')
                    ->label('اسم المقاول')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('project_location')
                    ->label('موقع المشروع')
                    ->limit(30)
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_contract_value')
                    ->label('القيمة الإجمالية')
                    ->money(fn($record) => $record->currency->code ?? 'USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'active' => 'success',
                        'completed' => 'primary',
                        'terminated' => 'warning',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'قيد الانتظار',
                        'active' => 'نشط',
                        'completed' => 'مكتمل',
                        'terminated' => 'منتهي',
                        'cancelled' => 'ملغي',
                    }),

                // Tables\Columns\TextColumn::make('start_date')
                //     ->label('تاريخ البدء')
                //     ->date('Y-m-d')
                //     ->sortable(),

                // Tables\Columns\TextColumn::make('end_date')
                //     ->label('تاريخ الانتهاء')
                //     ->date('Y-m-d')
                //     ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('حالة العقد')
                    ->options([
                        'pending' => 'قيد الانتظار',
                        'active' => 'نشط',
                        'completed' => 'مكتمل',
                        'terminated' => 'منتهي',
                        'cancelled' => 'ملغي',
                    ]),

                // Tables\Filters\Filter::make('start_date')
                //     ->label('تاريخ البدء')
                //     ->form([
                //         Forms\Components\DatePicker::make('start_from')
                //             ->label('من تاريخ'),
                //         Forms\Components\DatePicker::make('start_until')
                //             ->label('إلى تاريخ'),
                //     ])
                //     ->query(function (Builder $query, array $data): Builder {
                //         return $query
                //             ->when(
                //                 $data['start_from'],
                //                 fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                //             )
                //             ->when(
                //                 $data['start_until'],
                //                 fn (Builder $query, $date): Builder => $query->whereDate('start_date', '<=', $date),
                //             );
                //     }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
                ExportContractToPdfAction::make(), // إضافة زر التصدير
                Tables\Actions\ViewAction::make()->label('عرض'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('إنشاء عقد جديد'),
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjectContracts::route('/'),
            'create' => Pages\CreateProjectContract::route('/create'),
            'edit' => Pages\EditProjectContract::route('/{record}/edit'),
            'view' => Pages\ViewContract::route('/{record}'),
        ];
    }
}
