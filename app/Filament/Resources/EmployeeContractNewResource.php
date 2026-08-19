<?php

namespace App\Filament\Resources;

use App\Filament\Actions\ExportEmployeeContractNewToPdfAction;
use App\Filament\Resources\EmployeeContractNewResource\Pages;
use App\Filament\Resources\EmployeeContractNewResource\RelationManagers;
use App\Models\EmployeeContractNew;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeContractNewResource extends Resource
{
    protected static ?string $model = EmployeeContractNew::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'عقود الموظفين (الجديد)';

    protected static ?string $modelLabel = 'عقد موظف';

    protected static ?string $pluralModelLabel = 'عقود الموظفين';

    protected static ?string $navigationGroup = 'إدارة الموارد البشرية';

    protected static ?int $navigationSort = 21;

    public static function form(Form $form): Form
    {
        $defaultjob_desc = 'يلتزم الطرف الثاني بأن يعمل لدى الطرف الأول كما يلي: <br/>
                الوظيفة: {{ $job_title }}
                في قسم: {{ $department }}
                مقرّ العمل: {{ $job_description }}
                <br/>
                ، ويكون مسؤولاً عن تنفيذ جميع المهام والواجبات المطلوبة منه بموجب هذا العقد.';
        $defaultcon_dur = 'اتفق الطرفان على أن هذا العقد يحكم العلاقة بين كل منهما لمدة سنة بالتقويم الميلادي تبدأ من تاريخ مباشرة الطرف الثاني العمل لدى الطرف الأول، ولا يعتبر هذا العقد ساري المفعول إلا بعد مباشرة الطرف الثاني العمل في مواقع الطرف الأول.';
        $defaulttest_dur = 'يكون الطرف الثاني تحت التجربة لمدة (3) ثلاثة شهور تبدأ من تاريخ مباشرته العمل الفعلي وللطرف الأول الحق في فسخ العقد خلال فترة التجربة دون إعلان أو مكافأة أو تعويض وذلك بموجب نظام العمل.';
        $defaultsal_con = 'يلتزم الطرف الأول بأن يدفع للطرف الثاني أجراً شهرياً مقداره
                <span class="highlight">{{ $basic_salary }} {{ $currency_symbol }}</span>
                بما يعادل مبلغاً قدره
                <span class="highlight">{{ $salary_usd }} دولار أمريكي</span>
                في نهاية كل شهر وذلك مقابل التزاماته المحددة في هذا العقد والنظام واللوائح التي يصدرها الطرف الأول';
        $defaultleave = 'يحق للطرف الثاني إجازة سنوية وفق الشروط التالية: <br/>
                1.		مجموع أيام الأجازة السنوية قدرها ( 15 ) أيام بعد إمضائه فترة التجربة.<br/>
                2.		يتولى الطرف الأول تحديد تاريخ بداية الإجازة ونهايتها وفق ما تسمح به ظروف العمل ، وفي جميع الأحوال يتوجب على الطرف الثاني التمتع بأجازته السنوية في الموعد الذي يحدده الطرف الأول وليس له الاحتجاج على ذلك، مع مراعاة أحكام نظام العمل .<br/>
                3.		للطرف الثاني بموافقة الطرف الأول أن يؤجل للسنة التالية أجازته السنوية أو أياماً منها، وليس له التنازل عنها.<br/>';
        $defaultvacation = '1. للموظف الحق بالتمتع بإجازة بأجر كامل في كافة أيام العطل الرسمية التي يقرها نظام العمل.<br/>
            2. يستحق الموظف إجازة بأجر كامل على النحو التالي:<br/>
                •	ثلاثة أيام في حالة زواجه.<br/>
                •	ثلاثة أيام في حالة وفاة أحد فروعه أو أصوله من الدرجة الأولى.<br/>
                •	ثلاثة أيام في حالة ولادة مولود له.<br/>
            3. يعطى الطرف الثاني إذا ثبت مرضه بموجب تقرير طبي صادر من الجهة المعتمدة لدى الطرف الأول إجازة مرضية بأجر كامل عن الثلاثين يوماً الأولى، وبثلاثة أرباع الأجر عن الستين يوماً التالية خلال السنة الواحدة، وفي جميع الأحوال يجوز للطرف الأول التحقق من صحة التقرير الطبي المقدم وإجراء الفحوصات الطبية التي يراها لإثبات اللياقة الطبية للطرف الثاني.<br/>
            4. يجوز للطرف الثاني الحصول على إجازة بدون أجر لمدة ثلاثين يوم في السنة بشرط موافقة الطرف الأول.';
        $defaultovertime = '1. للشركة الحق في تشغيل من يلزم من موظفيها خلال أيام العطلات الرسمية على أن تدفع له أجرا إضافيا طبقا لأحكام نظام العمل.<br/>
            2. في حالة تكليف الموظف بالعمل خارج أوقات العمل الرسمي يستحق أجراً إضافياً مقداره 30% في الساعة بالإضافة إلى أجرة عن كل ساعة عمل إضافي وذلك حسب نظام العمل.<br/>
            3. لا يحق للطرف الثاني المطالبة بأجر عن ساعات العمل الإضافية إلا إذا كان قد كلف رسمياً بالعمل الإضافي من قبل الطرف الأول ــ ومن خوله هذه الصلاحية ولا يعتد بغير هذا التكلف الرسمي كدليل على ذلك العمل الإضافي.<br/>';
        $defaultconditions = '1. يكون نظام العمل الساري المفعول في سوريا النظام الوحيد الذي يرجع إليه في كل ما يرد به نص في هذا العقد، وكل نزاع ينشأ بخصوص تفسير هذا العقد يكون الفصل فيه للجهة القضائية وفقاً لنظام العمل.<br/>
            2. يلتزم الطرف الثاني بأداء العمل الذي يكلفه به الطرف الأول بنفسه ولا يجوز له الإنابة في أداء العمل لشخص آخر أو أن يسنده إلى غيره ولو كان تحت إشرافه.<br/>
            3. المخططات والأعمال التي يطورها إليها الطرف الثاني خلال سريان هذا العقد مما يتصل بأعمال وظيفته تكون حقا كاملا للطرف الأول. ولايحق للطرف الثاني نسحها أو بيعها بعد التعديل.<br/>
            4. يلتزم الطرف الثاني بأداء العمل طبقا للأصول العلمية والفنية وقواعد المهنة والتوجيهات التي يصدرها إليه الطرف الأول.<br/>
            5. يلتزم الطرف الثاني باللوائح والقواعد والتعليمات التي يصدرها الطرف الأول والواجبات والمحظورات المنصوص عليها في النظام والعقد، ومراعاة التعليمات والقواعد والإجراءات الخاصة بأمن وسلامة البيانات الوقائية لأماكن وأدوات وآلات العمل.<br/>
            6. يلتزم الطرف الثاني بالمحافظة ممتلكات الشركة وعدم السماح بالتضارب في المصالح بينة وبينها. <br/>
            7. يلتزم الطرف الثاني بمراعاته القوانين والعادات والتقاليد السارية في سوريا ، ويكون مسؤولا مسؤولية كاملة عن سلوكه وذلك بما يتماشى مع هذه القوانين والأعراف.<br/>
            8. لا يجوز حسم أي مبلغ من أجر الطرف الثاني لقاء حقوق خاصة إلا في الحالات التالية:<br/>
 	            • اشتراكات التأمينات الاجتماعية المستحقة عل الموظف.<br/>
 	            • الغرامات التي توقع على الموظف وفق النظام بسبب المخالفات التي يرتكبها، وكذلك المبلغ التي تقتطع منه مقابل ما أتلفه.<br/>
 	            • استرداد القروض والسلف المالية أو ما دفع إلى الموظف زيادة عن حقه بشرط أن لا يزيد مقدار هذا الحسم عن (10%) من أجره الشهري.<br/>
 	            • كل دين يستوفى إنفاذاً لأي حكم قضائي.<br/>
            9. يلتزم الطرف الثاني بالمحافظة على أسرار العمل سواء أثناء فترة خدمته أو بعد انتهائها ولا يحق له خلال سريان هذا العقد أن يعمل لدى الغير بأجر أو بدون أجر في تطوير مخططات مشابهة أو منافسة لمشاريع الشركة، وسواء كان ذلك خلال أو خارج أوقات الدوام الرسمي للطرف الأول، وأن يكرس وقت العمل الرسمي لأداء عمله، وأن يبادر إلى تقديم العون والمساعدة لزملائه في العمل دون أن يشترط لذلك أجرا إضافيا أو مكافأة خاصة، وقد اتفق الطرفان على أن يعتبر إخلال الطرف الثاني بهذا الالتزام إخلالا بالتزام جوهري بعقد العمل الموقع بينهما.';
        $defaultrenew = '1. يتجدد هذا العقد بين الطرفين بعد انتهاء مدته الأصلية لمدة أخرى مماثلة ، وفي حالة إستمرار الطرفين في تنفيذ هذا العقد بعد التجديد الأول يعتبر العقد مجدداً لفترة غير محددة.<br/>
            2. في جميع الأحوال وفي حالة رغبة أحد الطرفين بعدم تجديد العقد يتوجب على الطرف الذي يرغب في إنهاء العقد إعطاء الطرف الآخر فترة إنذار لا تقل مدتها عن ( 30 ) ثلاثين يوما ويجب أن يكون الإخطار كتابيا ويسلم إلى الطرف الموجه إليه.<br/>
            3. يحق للطرف الأول فسخ العقد بدون مكافأة أو سبق إعلان أو تعويض أو تحمل نفقات في الحالات الوارده في قانون العاملين الموحد في سوريا وهي على النحو التالي::<br/>
 	            • إذا وقع من الطرف الثاني أي اعتداء على الطرف الأول أو من يمثله أثناء العمل أو بسببه.<br/>
 	            • إذا لم يقم الطرف الثاني بتأدية الالتزامات الجوهرية المترتبة عليه،أو لم يطع الأوامر المشروعة، أو لم يراع عمدا التعليمات المبلغ بها من قبل رؤسائه.<br/>
 	            • إذا ثبت أن الطرف الثاني أفشى الأسرار الخاصة بالطرف الأول أو بعملائة.<br/>
 	            • إذا ارتكب الطرف الثاني خطأ عمدا بقصد الحاق خسارة ماديه بالطرف الأول.<br/>
 	            • إذا تغيب الطرف الثاني دون سبب مشروع أكثر من عشرين يوما خلال السنة الواحدة أو أكثر من عشر أيام متتالية.<br/>
 	            • لجوء الطرف الثاني إلى التزوير للحصول على العمل.<br/>
            4. يستحق الموظف مكافأة نهاية الخدمة حسب نظام العمل ويتم إحتسابها على أساس الراتب الأساسي بالإضافة للبدلات النظامية المنصوص عليها في عقد العمل ولا يدخل في ذلك المكافآت والحوافز والعمولات والنسب من أثمان المبيعات والتي تكون بطبيعتها قابلة للزيادة أو النقصان.';
        $defaultsystem_notes = 'يقر الطرف الثاني بأنه قد اطلع على لائحة نظام العمل الأساسية ولائحة المكافآت والجزاءات للطرف الأول والمعتمدة من وزارة العمل';

        return $form
            ->schema([
                Forms\Components\Section::make('معلومات العقد')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label('الموظف')
                            ->relationship('employee', 'name_ar')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),
                        Forms\Components\DatePicker::make('start_date')
                            ->label('تاريخ البدء')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->firstDayOfWeek(7)
                            ->closeOnDateSelection()
                            ->native(false),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('تاريخ الانتهاء')
                            ->displayFormat('d/m/Y')
                            ->firstDayOfWeek(7)
                            ->closeOnDateSelection()
                            ->native(false),
                        Forms\Components\Select::make('contract_type')
                            ->label('نوع العقد')
                            ->options([
                                'full_time' => 'دوام كامل',
                                'part_time' => 'دوام جزئي',
                                'daily' => 'يومي',
                                'contractor' => 'مقاول',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'active' => 'نشط',
                                'expired' => 'منتهي',
                                'terminated' => 'ملغي',
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('معلومات الشركة')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('اسم الشركة')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('company_commercial_registration')
                            ->label('رقم السجل التجاري')
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('company_registration_date')
                            ->label('تاريخ التسجيل')
                            ->displayFormat('d/m/Y')
                            ->firstDayOfWeek(7)
                            ->closeOnDateSelection()
                            ->native(false),
                        Forms\Components\TextInput::make('company_registration_source')
                            ->label('مصدر التسجيل')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('company_general_manager_name')
                            ->label('اسم المدير العام')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('company_representative_name')
                            ->label('اسم الممثل القانوني')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('company_address')
                            ->label('عنوان الشركة')
                            ->rows(2),
                        Forms\Components\TextInput::make('company_phone')
                            ->label('هاتف الشركة')
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('تفاصيل إضافية')
                    ->schema([
                        Forms\Components\TextInput::make('probation_period_days')
                            ->label('فترة التجربة (أيام)')
                            ->required()
                            ->numeric()
                            ->default(90),
                        Forms\Components\TextInput::make('working_hours')
                            ->label('ساعات العمل')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('working_days')
                            ->label('أيام العمل')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('محتوى العقد')
                    ->schema([
                        Forms\Components\RichEditor::make('job_desc')
                            ->label('وصف الوظيفة')
                            ->default($defaultjob_desc)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('con_dur')
                            ->label('مدة العقد')
                            ->default($defaultcon_dur)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('test_dur')
                            ->label('فترة التجربة')
                            ->default($defaulttest_dur)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('sal_con')
                            ->label('شروط الراتب')
                            ->default($defaultsal_con)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('leave')
                            ->label('الإجازات')
                            ->default($defaultleave)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('vacation')
                            ->label('العطلات')
                            ->default($defaultvacation)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('overtime')
                            ->label('العمل الإضافي')
                            ->default($defaultovertime)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('conditions')
                            ->label('الشروط العامة')
                            ->default($defaultconditions)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('renew')
                            ->label('التجديد')
                            ->default($defaultrenew)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('system_notes')
                            ->label('ملاحظات النظام')
                            ->default($defaultsystem_notes)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('no_copies')
                            ->label('عدد النسخ')
                            ->required()
                            ->numeric()
                            ->default(2),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name_ar')
                    ->label('الموظف')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('تاريخ البدء')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('تاريخ الانتهاء')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('contract_type')
                    ->label('نوع العقد')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'full_time' => 'دوام كامل',
                        'part_time' => 'دوام جزئي',
                        'daily' => 'يومي',
                        'contractor' => 'مقاول',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'expired' => 'warning',
                        'terminated' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'نشط',
                        'expired' => 'منتهي',
                        'terminated' => 'ملغي',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'active' => 'نشط',
                        'expired' => 'منتهي',
                        'terminated' => 'ملغي',
                    ]),
                Tables\Filters\SelectFilter::make('contract_type')
                    ->label('نوع العقد')
                    ->options([
                        'full_time' => 'دوام كامل',
                        'part_time' => 'دوام جزئي',
                        'daily' => 'يومي',
                        'contractor' => 'مقاول',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                ExportEmployeeContractNewToPdfAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListEmployeeContractNews::route('/'),
            'create' => Pages\CreateEmployeeContractNew::route('/create'),
            'edit' => Pages\EditEmployeeContractNew::route('/{record}/edit'),
        ];
    }
}
