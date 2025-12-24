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

        $defaultPreamble = 'حيث إن الطرف الأول يرغب في تنفيذ أعمال بناء وتشطيب لمشروعه الكائن في {{ $project_location }}، وحيث إن الطرف الثاني لديه الخبرة والإمكانيات اللازمة لتنفيذ هذه الأعمال، فقد اتفق الطرفان على ما يلي:';

        $defaultSubject = 'يتعهد الطرف الثاني بتنفيذ جميع الأعمال الخاصة بـ الإنشاء والتشييد والتشطيب والإكساء لمبنى الطرف الأول، وتشمل على سبيل المثال لا الحصر:
<ul>
    <li>أعمال الحفر والأساسات والهيكل الخرساني.</li>
    <li>أعمال البناء واللياسة والدهان.</li>
    <li>أعمال الكهرباء والسباكة والتكييف.</li>
    <li>أعمال الأرضيات، الجدران، الأسقف، الأبواب، النوافذ، الديكور والإكساء الكامل حسب المواصفات.</li>
</ul>';

        $defaultSpecifications = 'يتم تنفيذ الأعمال طبقاً للمخططات الهندسية والمواصفات الفنية المعتمدة من الطرف الأول أو من المهندس المشرف، ويُعد أي تعديل لاحق بموجب ملحق اتفاق خطي موقع من الطرفين.';

        $defaultDuration = 'مدة تنفيذ المشروع هي ({{ $execution_period }} يوم) تبدأ من تاريخ تسليم الموقع، على أن يلتزم الطرف الثاني بالجدول الزمني المتفق عليه.
وفي حال التأخير غير المبرر، يحق للطرف الأول فرض غرامة تأخير بنسبة ({{ $delay_penalty_percentage }}%) عن كل يوم تأخير بعد المدة المحددة، بحد أقصى ({{ $max_penalty_percentage }}%) من قيمة العقد.';

        $defaultPayment = 'قيمة العقد الإجمالية هي مبلغ وقدره ({{ $total_contract_value_formatted }} {{ $currency_symbol }}) تُدفع على النحو التالي:
<ul>
    <li>دفعة أولى: عند توقيع العقد بنسبة ({{ $initial_payment_percentage }}%) من قيمة العقد.</li>
    <li>دفعة ثانية: بعد إنجاز مرحلة الهيكل الخرساني بنسبة ({{ $concrete_stage_payment_percentage }}%).</li>
    <li>دفعة ثالثة: بعد الانتهاء من أعمال التشطيب بنسبة ({{ $finishing_stage_payment_percentage }}%).</li>
    <li>دفعة نهائية: بعد التسليم النهائي وخلو المشروع من الملاحظات بنسبة ({{ $final_payment_percentage }}%).</li>
</ul>';

        $defaultObligations = '<span class="text-bold">التزامات الطرف الثاني (المقاول):</span>
<ul>
    <li>تنفيذ جميع الأعمال حسب الأصول الفنية والمواصفات المعتمدة.</li>
    <li>استخدام مواد مطابقة للمواصفات القياسية.</li>
    <li>الالتزام بوسائل السلامة في موقع العمل.</li>
    <li>إصلاح أي عيب أو خلل يظهر خلال فترة الضمان.</li>
</ul>

<span class="text-bold">التزامات الطرف الأول (المالك):</span>
<ul>
    <li>تسليم الموقع خالياً من العوائق.</li>
    <li>تسديد الدفعات حسب الجدول الزمني.</li>
    <li>اعتماد المخططات والاختيارات في الوقت المحدد دون تأخير.</li>
</ul>';

        $defaultWarranty = 'يتعهد الطرف الثاني بضمان الأعمال المنفذة لمدة (12 شهراً) من تاريخ التسليم النهائي، ضد أي عيب في التنفيذ أو المواد، ويتحمل نفقات الإصلاح خلال هذه المدة.';

        $defaultTermination = 'يحق للطرف الأول فسخ العقد في الحالات التالية:
<ul>
    <li>تأخر الطرف الثاني عن تنفيذ الأعمال دون مبرر.</li>
    <li>إخلاله بالشروط أو المواصفات المتفق عليها.</li>
    <li>توقفه عن العمل دون سبب وجيه لأكثر من (15) يوماً.</li>
</ul>
وفي حال الفسخ، يُلزم الطرف الثاني بتسليم جميع المواد والأعمال المنفذة حتى تاريخه ودفع أي تعويض يترتب على ذلك.';

        $defaultArbitration = 'في حال حدوث أي خلاف بين الطرفين، يتم حله وديًا، وإن تعذر ذلك يُحال النزاع إلى التحكيم وفق القوانين المعمول بها في ({{ $arbitration_location }}).';

        $defaultGeneralTerms = '<ul>
    <li>لا يجوز لأي طرف التنازل عن العقد أو جزء منه دون موافقة الطرف الآخر كتابةً.</li>
    <li>هذا العقد يشكل الاتفاق الكامل بين الطرفين ويلغي ما قبله من تفاهمات شفوية أو كتابية.</li>
    <li>يُعد كل من الطرفين مسؤولاً عن التزاماته المنصوص عليها في هذا العقد.</li>
</ul>';

        return $form
            ->schema([
                // تبويب معلومات الطرفين
                Forms\Components\Wizard::make([
                        Forms\Components\Wizard\Step::make('الطرف الأول (المالك)')
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

                        Forms\Components\Wizard\Step::make('الطرف الثاني (المقاول)')
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

                        Forms\Components\Wizard\Step::make('معلومات المشروع')
                            ->schema([
                                Forms\Components\Section::make('تفاصيل المشروع')
                                    ->schema([
                                        Forms\Components\Textarea::make('project_location')
                                            ->label('موقع المشروع')
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Forms\Components\Wizard\Step::make('المدة والجدول الزمني')
                            ->schema([
                                Forms\Components\Section::make('فترة التنفيذ')
                                    ->schema([
                                        Forms\Components\TextInput::make('execution_period')
                                            ->label('مدة التنفيذ (بالأيام)')
                                            ->required()
                                            ->numeric()
                                            ->minValue(1),
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

                        Forms\Components\Wizard\Step::make('القيمة والدفعات')
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

                        Forms\Components\Wizard\Step::make('الضمان والأحكام')
                            ->schema([
                                Forms\Components\Section::make('التحكيم')
                                    ->schema([
                                        Forms\Components\TextInput::make('arbitration_location')
                                            ->label('مكان التحكيم')
                                            ->required()
                                            ->maxLength(255),
                                    ]),
                            ]),

                        Forms\Components\Wizard\Step::make('محتوى العقد')
                            ->schema([
                                Forms\Components\Section::make('محتوى العقد')
                                    ->description('يمكنك تعديل كل قسم من أقسام العقد. استخدم المتغيرات بين {} لتعويض البيانات تلقائياً.')
                                    ->collapsible()
                                    ->schema([
                                        Forms\Components\RichEditor::make('preamble_content')
                                            ->label('المقدمة')
                                            ->default($defaultPreamble)
                                            ->helperText('المتغيرات المتاحة: {project_location}')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'strike',
                                                'bulletList',
                                                'orderedList',
                                                'alignRight',
                                                'alignJustify',
                                                'undo',
                                                'redo',
                                            ])
                                            ->columnSpanFull(),

                                        Forms\Components\RichEditor::make('subject_content')
                                            ->label('موضوع العقد')
                                            ->default($defaultSubject)
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'strike',
                                                'bulletList',
                                                'orderedList',
                                                'alignRight',
                                                'alignJustify',
                                                'undo',
                                                'redo',
                                            ])
                                            ->columnSpanFull(),

                                        Forms\Components\RichEditor::make('specifications_content')
                                            ->label('المواصفات والمخططات')
                                            ->default($defaultSpecifications)
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'strike',
                                                'bulletList',
                                                'orderedList',
                                                'alignRight',
                                                'alignJustify',
                                                'undo',
                                                'redo',
                                            ])
                                            ->columnSpanFull(),

                                        Forms\Components\RichEditor::make('duration_content')
                                            ->label('مدة التنفيذ')
                                            ->default($defaultDuration)
                                            ->helperText('المتغيرات المتاحة: {execution_period}, {delay_penalty_percentage}, {max_penalty_percentage}')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'strike',
                                                'bulletList',
                                                'orderedList',
                                                'alignRight',
                                                'alignJustify',
                                                'undo',
                                                'redo',
                                            ])
                                            ->columnSpanFull(),

                                        Forms\Components\RichEditor::make('payment_content')
                                            ->label('القيمة وطريقة الدفع')
                                            ->default($defaultPayment)
                                            ->helperText('المتغيرات المتاحة: {total_contract_value_formatted}, {currency_symbol}, {initial_payment_percentage}, {concrete_stage_payment_percentage}, {finishing_stage_payment_percentage}, {final_payment_percentage}')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'strike',
                                                'bulletList',
                                                'orderedList',
                                                'alignRight',
                                                'alignJustify',
                                                'undo',
                                                'redo',
                                            ])
                                            ->columnSpanFull(),

                                        Forms\Components\RichEditor::make('obligations_content')
                                            ->label('الالتزامات')
                                            ->default($defaultObligations)
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'strike',
                                                'bulletList',
                                                'orderedList',
                                                'alignRight',
                                                'alignJustify',
                                                'undo',
                                                'redo',
                                            ])
                                            ->columnSpanFull(),

                                        Forms\Components\RichEditor::make('warranty_content')
                                            ->label('الضمان والصيانة')
                                            ->default($defaultWarranty)
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'strike',
                                                'bulletList',
                                                'orderedList',
                                                'alignRight',
                                                'alignJustify',
                                                'undo',
                                                'redo',
                                            ])
                                            ->columnSpanFull(),

                                        Forms\Components\RichEditor::make('termination_content')
                                            ->label('فسخ العقد')
                                            ->default($defaultTermination)
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'strike',
                                                'bulletList',
                                                'orderedList',
                                                'alignRight',
                                                'alignJustify',
                                                'undo',
                                                'redo',
                                            ])
                                            ->columnSpanFull(),

                                        Forms\Components\RichEditor::make('arbitration_content')
                                            ->label('التحكيم')
                                            ->default($defaultArbitration)
                                            ->helperText('المتغيرات المتاحة: {arbitration_location}')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'strike',
                                                'bulletList',
                                                'orderedList',
                                                'alignRight',
                                                'alignJustify',
                                                'undo',
                                                'redo',
                                            ])
                                            ->columnSpanFull(),

                                        Forms\Components\RichEditor::make('general_terms_content')
                                            ->label('أحكام عامة')
                                            ->default($defaultGeneralTerms)
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'strike',
                                                'bulletList',
                                                'orderedList',
                                                'alignRight',
                                                'alignJustify',
                                                'undo',
                                                'redo',
                                            ])
                                            ->columnSpanFull(),

                                        Forms\Components\RichEditor::make('notes_content')
                                            ->label('ملاحظات إضافية')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'strike',
                                                'bulletList',
                                                'orderedList',
                                                'alignRight',
                                                'alignJustify',
                                                'undo',
                                                'redo',
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Forms\Components\Wizard\Step::make('حالة العقد')
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
