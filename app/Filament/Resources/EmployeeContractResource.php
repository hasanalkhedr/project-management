<?php

namespace App\Filament\Resources;

use App\Filament\Actions\ExportEmployeeContractToPdfAction;
use App\Filament\Resources\EmployeeContractResource\Pages;
use App\Models\EmployeeContract;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeContractResource extends Resource
{
    protected static ?string $model = EmployeeContract::class;

    protected static ?string $navigationIcon = 'heroicon-s-document-duplicate';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'عقود الموظفين';

    protected static ?string $modelLabel = 'عقد موظف';

    protected static ?string $pluralModelLabel = 'عقود الموظفين';

    public static function form(Form $form): Form
    {
        $defaultPreamble = 'حيث التقت إرادة الطرفين في العمل والتعاون بينهما، تم الإيجاب والقبول واتفقا وهما بكامل الأوصاف المعتبرة شرعا على التالي:';

//         $defaultSubject = 'يتعهد الطرف الثاني بالعمل لدى الطرف الأول في وظيفة {{ $job_title }} في قسم {{ $department }}، ويكون مسؤولاً عن تنفيذ جميع المهام والواجبات المطلوبة منه بموجب هذا العقد.';

//         $defaultResponsibilities = '<span class="text-bold">المسؤوليات الرئيسية:</span>
// <ul>
//     <li>تنفيذ المهام والواجبات المطلوبة بدقة وكفاءة.</li>
//     <li>الالتزام بساعات العمل المحددة في هذا العقد.</li>
//     <li>المحافظة على ممتلكات الشركة وأسرارها التجارية.</li>
//     <li>التعاون مع الزملاء والإدارة لتحقيق أهداف الشركة.</li>
//     <li>الالتزام بالسياسات والإجراءات الداخلية للشركة.</li>
// </ul>';

//         $defaultWorkingHours = 'ساعات العمل: {{ $working_hours }}
// أيام العمل: {{ $working_days }}
// يتم تحديد جدول العمل من قبل الإدارة ويمكن تعديله حسب متطلبات العمل.';

//         $defaultSalary = 'الراتب الأساسي: {{ $basic_salary }} {{ $currency_symbol }}
// بدل السكن: {{ $housing_allowance }} {{ $currency_symbol }}
// بدل المواصلات: {{ $transportation_allowance }} {{ $currency_symbol }}
// بدلات أخرى: {{ $other_allowances }} {{ $currency_symbol }}
// الإجمالي: {{ $total_salary_formatted }} {{ $currency_symbol }}
// يتم صرف الراتب في نهاية كل شهر ميلادي.';

//         $defaultBenefits = '<span class="text-bold">المزايا والحقوق:</span>
// <ul>
//     <li>إجازة سنوية مدفوعة الأجر: 30 يوماً سنوياً.</li>
//     <li>إجازة مرضية: 30 يوماً سنوياً.</li>
//     <li>تأمين طبي حسب سياسة الشركة.</li>
//     <li>تذكرة سفر سنوية للموظف.</li>
//     <li>مكافأة نهاية الخدمة حسب قانون العمل.</li>
// </ul>';

//         $defaultLeave = '<span class="text-bold">الإجازات:</span>
// <ul>
//     <li>الإجازة السنوية: 30 يوماً بعد سنة من الخدمة.</li>
//     <li>الإجازة المرضية: 30 يوماً في السنة بشهادة طبية.</li>
//     <li>إجازة الأعياد والمناسبات الرسمية حسب قانون العمل.</li>
//     <li>إجازة الأمومة للإناث حسب قانون العمل.</li>
// </ul>';

//         $defaultTermination = 'يحق لأي من الطرفين إنهاء هذا العقد في الحالات التالية:
// <ul>
//     <li>بإشعار مسبق مدته 30 يوماً.</li>
//     <li>في حال إخلال الطرف الآخر بالشروط الجوهرية للعقد.</li>
//     <li>في حال استقالة الموظف مع إشعار مسبق 30 يوماً.</li>
//     <li>في حال فصل الموظف لأسباب مشروعة.</li>
// </ul>
// في حال الفصل التعسفي، يحق للموظف مكافأة نهاية الخدمة كاملة.';

//         $defaultConfidentiality = 'يلتزم الطرف الثاني بالحفاظ على سرية جميع المعلومات والمستندات والبيانات الخاصة بالشركة، وعدم الإفصاح عنها لأي طرف ثالث سواء أثناء فترة العمل أو بعد انتهائها.';

//         $defaultGeneralTerms = '<ul>
//     <li>هذا العقد يشكل الاتفاق الكامل بين الطرفين ويلغي ما قبله من تفاهمات.</li>
//     <li>أي تعديل على هذا العقد يجب أن يكون خطياً وموقعاً من الطرفين.</li>
//     <li>يخضع هذا العقد لقوانين العمل المعمول بها في الدولة.</li>
//     <li>في حال وجود أي خلاف، يتم حله ودياً أولاً ثم عن طريق المحاكم المختصة.</li>
// </ul>';



$defaultjob_desc = 'يلتزم الطرف الثاني بأن يعمل لدى الطرف الأول كما يلي: <br/>
                الوظيفة: {{ $job_title }}
                في قسم: {{ $department }}
                مقرّ العمل: {{ $job_description }}
                <br/>
                ، ويكون مسؤولاً عن تنفيذ جميع المهام والواجبات المطلوبة منه بموجب هذا العقد.
      ';
$defaultcon_dur = 'اتفق الطرفان على أن هذا العقد يحكم العلاقة بين كل منهما لمدة سنة بالتقويم الميلادي تبدأ من تاريخ مباشرة الطرف الثاني العمل لدى الطرف الأول، ولا يعتبر هذا العقد ساري المفعول إلا بعد مباشرة الطرف الثاني العمل في مواقع الطرف الأول.';
$defaulttest_dur = 'يكون الطرف الثاني تحت التجربة لمدة (3) ثلاثة شهور تبدأ من تاريخ مباشرته العمل الفعلي وللطرف الأول الحق في فسخ العقد خلال فترة التجربة دون إعلان أو مكافأة أو تعويض وذلك بموجب نظام العمل.';
$defaultstart_date = 'يلتزم الطرف الثاني مباشرة العمل خلال فترة أقصاها: 15 يوما اعتبارا من تاريخ توقيع هذا العقد وإلا أعتبر هذا العقد مفسوخا من جانبه.';
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
            3. لا يحق للطرف الثاني المطالبة بأجر عن ساعات العمل الإضافية إلا إذا كان قد كلف رسمياً بالعمل الإضافي من قبل الطرف الأول ــ ومن خوله هذه الصلاحية ولا يعتد بغير هذا التكلف الرسمي كدليل على ذلك العمل الإضافي.<br/>
';
$defaultworking_hours = '1. يلتزم الطرف الثاني بأن يعمل في خدمة الطرف الأول بمعدل ( 48 ) ساعة أسبوعياً، ولا يدخل في حساب ساعات العمل اليومية الفعلية الفترات المخصصة للراحة والصلاة والطعام، ويعتبر يوم الجمعة راحة أسبوعية للطرف الثاني بأجر كامل.<br/>
            2. يثبت الطرف الثاني حضوره وانصرافه حسب الطريقة التي يحددها الطرف الأول لمتابعة ساعات الدوام.<br/>';
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
            4. يستحق الموظف مكافأة نهاية الخدمة حسب نظام العمل ويتم إحتسابها على أساس الراتب الأساسي بالإضافة للبدلات النظامية المنصوص عليها في عقد العمل ولا يدخل في ذلك المكافآت والحوافز والعمولات والنسب من أثمان المبيعات والتي تكون بطبيعتها قابلة للزيادة أو النقصان.
            ';
$defaultsystem_notes = '            يقر الطرف الثاني بأنه قد اطلع على لائحة نظام العمل الأساسية ولائحة المكافآت والجزاءات للطرف الأول والمعتمدة من وزارة العمل';
$defaultno_copies = 'تم تحرير هذا العقد بمدينة_____________ في يوم ____________ الموافق: ___ / ___ / _____ من نسختين لكل منهما نفس القوة والأثر ويحتفظ كل طرف بنسخة للعمل بموجبها والتقيد بأحكامها';

        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('معلومات الشركة')
                        ->schema([
                            Forms\Components\Section::make('بيانات الشركة')
                                ->description('معلومات الطرف الأول (صاحب العمل)')
                                ->schema([
                                    Forms\Components\TextInput::make('company_name')
                                        ->label('اسم الشركة')
                                        ->required()
                                        ->default('شركة أبراج الريان للمقاولات')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('company_commercial_registration')
                                        ->label('السجل التجاري')
                                        ->maxLength(100),
                                    Forms\Components\TextInput::make('company_registration_date')
                                        ->label('تاريخ السجل التجاري')
                                        ->maxLength(50),
                                    Forms\Components\TextInput::make('company_registration_source')
                                        ->label('مصدر السجل التجاري')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('company_general_manager_name')
                                        ->label('اسم المدير العام')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('company_representative_name')
                                        ->label('اسم الممثل القانوني')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('company_phone')
                                        ->label('هاتف الشركة')
                                        ->required()
                                        ->tel()
                                        ->maxLength(20),
                                    Forms\Components\Textarea::make('company_address')
                                        ->label('عنوان الشركة')
                                        ->required()
                                        ->columnSpanFull(),
                                ])->columns(2),
                        ]),

                    Forms\Components\Wizard\Step::make('معلومات الموظف')
                        ->schema([
                            Forms\Components\Section::make('بيانات الموظف')
                                ->description('معلومات الطرف الثاني (الموظف)')
                                ->schema([
                                    Forms\Components\TextInput::make('employee_name')
                                        ->label('اسم الموظف')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('employee_id_number')
                                        ->label('الرقم الوطني')
                                        ->required()
                                        ->maxLength(50),
                                    Forms\Components\TextInput::make('employee_id_issue_number')
                                        ->label('رقم الهوية')
                                        ->required()
                                        ->maxLength(50),
                                    Forms\Components\TextInput::make('employee_nationality')
                                        ->label('الجنسية')
                                        ->required()
                                        ->maxLength(50),
                                    Forms\Components\DatePicker::make('employee_id_issue_date')
                                        ->label('تاريخ الإصدار')
                                        ->required()
                                        ,
                                    Forms\Components\TextInput::make('employee_id_issue_place')
                                        ->label('مكان الإصدار')
                                        ->required()
                                        ->maxLength(50),

                                    Forms\Components\TextInput::make('employee_email')
                                        ->label('البريد الإلكتروني')
                                        ->email()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('employee_phone')
                                        ->label('هاتف الموظف')
                                        ->required()
                                        ->tel()
                                        ->maxLength(20),
                                    Forms\Components\Textarea::make('employee_address')
                                        ->label('عنوان الموظف الحالي')
                                        ->required()
                                        ->columnSpanFull(),
                                    Forms\Components\Textarea::make('employee_permanent_address')
                                        ->label('عنوان الموظف الدائم')
                                        ->required()
                                        ->columnSpanFull(),
                                ])->columns(2),
                        ]),

                    Forms\Components\Wizard\Step::make('معلومات الوظيفة')
                        ->schema([
                            Forms\Components\Section::make('تفاصيل الوظيفة')
                                ->schema([
                                    Forms\Components\TextInput::make('job_title')
                                        ->label('المسمى الوظيفي')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('department')
                                        ->label('القسم')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('job_description')
                                        ->label('موقع العمل')
                                        ->maxLength(255),
                                    Forms\Components\DatePicker::make('contract_date')
                                        ->label('تاريخ العقد')
                                        ->required()
                                        ->displayFormat('d/m/Y')
                                        ->firstDayOfWeek(7)
                                        ->closeOnDateSelection()
                                        ->native(false),
                                ])->columns(4),
                        ]),

                    Forms\Components\Wizard\Step::make('الراتب والمزايا')
                        ->schema([
                            Forms\Components\Section::make('الراتب الأساسي')
                                ->schema([
                                    Forms\Components\TextInput::make('basic_salary')
                                        ->label('الراتب الأساسي بالليرة السورية الجديدة')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->prefix('ل.س'),
                                    Forms\Components\TextInput::make('basic_salary_usd')
                                        ->label('الراتب الأساسي بالدولار')
                                        ->required()
                                        ->numeric()
                                        ->minValue(0)
                                        ->prefix('$'),

                                ])->columns(2),

                            // Forms\Components\Section::make('البدلات')
                            //     ->schema([
                            //         Forms\Components\TextInput::make('housing_allowance')
                            //             ->label('بدل السكن')
                            //             ->numeric()
                            //             ->minValue(0)
                            //             ->default(0)
                            //             ->prefix('$'),
                            //         Forms\Components\TextInput::make('transportation_allowance')
                            //             ->label('بدل المواصلات')
                            //             ->numeric()
                            //             ->minValue(0)
                            //             ->default(0)
                            //             ->prefix('$'),
                            //         Forms\Components\TextInput::make('other_allowances')
                            //             ->label('بدلات أخرى')
                            //             ->numeric()
                            //             ->minValue(0)
                            //             ->default(0)
                            //             ->prefix('$'),
                            //     ])->columns(3),
                        ]),

                    // Forms\Components\Wizard\Step::make('مدة وساعات العمل')
                    //     ->schema([
                    //         Forms\Components\Section::make('فترة العقد')
                    //             ->schema([
                    //                 Forms\Components\DatePicker::make('start_date')
                    //                     ->label('تاريخ البدء')
                    //                     ->required()
                    //                     ->displayFormat('d/m/Y')
                    //                     ->firstDayOfWeek(7)
                    //                     ->closeOnDateSelection()
                    //                     ->native(false),
                    //                 Forms\Components\DatePicker::make('end_date')
                    //                     ->label('تاريخ الانتهاء')
                    //                     ->displayFormat('d/m/Y')
                    //                     ->firstDayOfWeek(7)
                    //                     ->closeOnDateSelection()
                    //                     ->native(false),
                    //                 Forms\Components\TextInput::make('probation_period_days')
                    //                     ->label('فترة التجربة (بالأيام)')
                    //                     ->numeric()
                    //                     ->minValue(0)
                    //                     ->default(90),
                    //             ])->columns(3),

                    //         Forms\Components\Section::make('ساعات العمل')
                    //             ->schema([
                    //                 Forms\Components\TextInput::make('working_hours')
                    //                     ->label('ساعات العمل اليومية')
                    //                     ->placeholder('مثال: 9 صباحاً - 5 مساءً')
                    //                     ->maxLength(255),
                    //                 Forms\Components\TextInput::make('working_days')
                    //                     ->label('أيام العمل')
                    //                     ->placeholder('مثال: الأحد - الخميس')
                    //                     ->maxLength(255),
                    //             ])->columns(2),
                    //     ]),

                    Forms\Components\Wizard\Step::make('محتوى العقد')
                        ->schema([
                            Forms\Components\Section::make('محتوى العقد')
                                ->description('يمكنك تعديل كل قسم من أقسام العقد')
                                ->collapsible()
                                ->schema([
                                    Forms\Components\RichEditor::make('preamble_content')
                                        ->label('المقدمة')
                                        ->default($defaultPreamble)
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

                                   /* Forms\Components\RichEditor::make('subject_content')
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

                                    Forms\Components\RichEditor::make('responsibilities_content')
                                        ->label('المسؤوليات')
                                        ->default($defaultResponsibilities)
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

                                    Forms\Components\RichEditor::make('working_hours_content')
                                        ->label('ساعات العمل')
                                        ->default($defaultWorkingHours)
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

                                    Forms\Components\RichEditor::make('salary_content')
                                        ->label('الراتب والمزايا')
                                        ->default($defaultSalary)
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

                                    Forms\Components\RichEditor::make('benefits_content')
                                        ->label('المزايا والحقوق')
                                        ->default($defaultBenefits)
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

                                    Forms\Components\RichEditor::make('leave_content')
                                        ->label('الإجازات')
                                        ->default($defaultLeave)
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
                                        ->label('إنهاء العقد')
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

                                    Forms\Components\RichEditor::make('confidentiality_content')
                                        ->label('السرية')
                                        ->default($defaultConfidentiality)
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
                                        ->columnSpanFull(),*/

                                    Forms\Components\RichEditor::make('job_desc')
                                        ->label('المادة /1/: بيانات الوظيفة:')
                                        ->default($defaultjob_desc)
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

                                    Forms\Components\RichEditor::make('con_dur')
                                        ->label('المادة /2/: مدّة العقد:')
                                        ->default($defaultcon_dur)
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

                                    Forms\Components\RichEditor::make('test_dur')
                                        ->label('المادة /3/: فترة التجربة:')
                                        ->default($defaulttest_dur)
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

                                    Forms\Components\RichEditor::make('start_date')
                                        ->label('المادة /4/: التاريخ المحدّد لمباشرة العمل:')
                                        ->default($defaultstart_date)
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

                                    Forms\Components\RichEditor::make('sal_con')
                                        ->label('المادة /5/: الأجر الشهري:')
                                        ->default($defaultsal_con)
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

                                    Forms\Components\RichEditor::make('leave')
                                        ->label('المادة /6/: الإجازة السنوية:')
                                        ->default($defaultleave)
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

                                    Forms\Components\RichEditor::make('vacation')
                                        ->label('المادة /7/: العطل الرسمية والإجازات الأخرى:')
                                        ->default($defaultvacation)
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

                                    Forms\Components\RichEditor::make('overtime')
                                        ->label('المادة /8/: العمل الإضافي:')
                                        ->default($defaultovertime)
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

                                    Forms\Components\RichEditor::make('working_hours')
                                        ->label('المادة /9/: ساعات العمل:')
                                        ->default($defaultworking_hours)
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

                                    Forms\Components\RichEditor::make('conditions')
                                        ->label('المادة /10/: شروط عامّة:')
                                        ->default($defaultconditions)
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

                                    Forms\Components\RichEditor::make('renew')
                                        ->label('المادة /11/: تجديد وفسخ العقد ونهاية الخدمة:')
                                        ->default($defaultrenew)
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

                                    Forms\Components\RichEditor::make('system_notes')
                                        ->label('المادة /12/: الاطلاع على اللائحة الداخلية:')
                                        ->default($defaultsystem_notes)
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

                                    Forms\Components\RichEditor::make('no_copies')
                                        ->label('المادة /13/: عدد نسخ العقد:')
                                        ->default($defaultno_copies)
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

                ])->columnSpanFull()
                ->skippable()
                ->persistStepInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contract_number')
                    ->label('رقم العقد')
                    ->default(fn($record) => 'EMP-CONTRACT-' . $record->id)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('employee_name')
                    ->label('اسم الموظف')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('job_title')
                    ->label('المسمى الوظيفي')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('department')
                    ->label('القسم')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('basic_salary')
                    ->label('الراتب الإجمالي')
                    ->money(fn($record) => 'ل.س')
                    ->sortable(),

                // Tables\Columns\TextColumn::make('start_date')
                //     ->label('تاريخ البدء')
                //     ->date('d/m/Y')
                //     ->sortable(),

                // Tables\Columns\TextColumn::make('end_date')
                //     ->label('تاريخ الانتهاء')
                //     ->date('d/m/Y')
                //     ->sortable()
                //     ->toggleable(),

                // Tables\Columns\TextColumn::make('status')
                //     ->label('الحالة')
                //     ->badge()
                //     ->color(fn(string $state): string => match ($state) {
                //         'pending' => 'gray',
                //         'active' => 'success',
                //         'completed' => 'primary',
                //         'terminated' => 'warning',
                //         'cancelled' => 'danger',
                //     }),

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
                        'pending' => 'قيد الانتظار',
                        'active' => 'نشط',
                        'completed' => 'مكتمل',
                        'terminated' => 'منتهي',
                        'cancelled' => 'ملغي',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                ExportEmployeeContractToPdfAction::make(),
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
            'index' => Pages\ListEmployeeContracts::route('/'),
            'create' => Pages\CreateEmployeeContract::route('/create'),
            'edit' => Pages\EditEmployeeContract::route('/{record}/edit'),
        ];
    }
}
