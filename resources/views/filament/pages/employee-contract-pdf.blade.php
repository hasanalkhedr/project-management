<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>عقد عمل - {{ $record->id }}</title>
    <link rel="stylesheet" href="{{public_path('css/emp_con.css')}}">
    <style>
        @font-face {
            font-family: 'almarai';
            font-style: normal;
            font-weight: bold;
            src: url('{{ storage_path('fonts/Almarai-ExtraBold.ttf') }}') format('truetype');
        }
    </style>
</head>

<body class="contract">
    <!-- Confidentiality Notice -->
    <div style="position: absolute; top: 10px; left: 10px; color: #999; font-size: 12px; font-weight: bold;">
        خاص وسرّي
    </div>

    <!-- Header Section -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="logo-cell" rowspan="2">
                    @if (file_exists($logo))
                        <img src="{{ $logo }}" class="logo" alt="شعار الشركة" />
                    @endif
                </td>

            </tr>
            <tr>
                <td class="title-cell">
                    <div class="report-title">عقد عمل</div>
                </td>
                <td class="right-cell" rowspan="1"></td>
            </tr>
            <tr>
                <td class="content-cell" colspan="2">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="text-align: right;">العقد رقم: EMP-CONTRACT-{{ $record->id }}</td>
                            <td style="text-align: center;">التاريخ: {{ $record->contract_date ? $record->contract_date->format('d/m/Y') : 'غير محدد' }}</td>
                            <td style="text-align: left;">اليوم: {{ $record->contract_date ? $record->contract_date->locale('ar')->dayName : 'غير محدد' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- Parties Information -->
    <div class="parties-section no-break">
        <div class="party-row">
            <div class="party-header" style="display: inline; white-space: nowrap; width: 90%;">الطرف الأول (صاحب العمل - الشركة) وبياناته كما يلي:</div>
            <div class="party-details">
                الإسم التجاري: <span class="highlight">{{ $record->company_name }}</span> ممثلة بمديرها العام السيد : <span class="highlight">{{ $record->company_general_manager_name }}</span>، رقم السجل التجاري: (<span class="highlight">{{ $record->company_commercial_registration }}</span>) تاريخ: (<span class="highlight">{{ $record->company_registration_date }}</span>)، مــصــدره: (أمانة السجل التجاري في <span class="highlight">{{ $record->company_registration_source }}</span>)
                {{-- <br> --}}
                ويمثله في هذا العقد: _السيد <span class="highlight">{{ $record->company_representative_name }}</span> المدير التنفيذي للشركة_
                {{-- <br> --}}
                العنوان: <span class="highlight">{{ $record->company_address }}</span> - الهاتف: <span class="highlight">{{ $record->company_phone }}</span>
            </div>
        </div>
        <div class="party-row">
            <div class="party-header" style="display: inline; white-space: nowrap; width: 90%;">الطرف الثاني: وبياناته كما يلي:</div>
            <div class="party-details">
                السيد: <span class="highlight">{{ $record->employee_name }}</span> من الجنسية: <span class="highlight">{{ $record->employee_nationality }}</span>
                {{-- <br> --}}
                الرقم الوطني: <span class="highlight">{{ $record->employee_id_number }}</span> رقم الهوية: <span class="highlight">{{ $record->employee_id_issue_number }}</span>
                {{-- <br> --}}
                تاريخ الاصدار: <span class="highlight">{{ $record->employee_id_issue_date }}</span> مكان الاصدار: <span class="highlight">{{ $record->employee_id_issue_place }}</span>
                {{-- <br> --}}
                رقم الهاتف: <span class="highlight">{{ $record->employee_phone }}</span> البريد الإلكتروني E-Mail: <span class="highlight">{{ $record->employee_email }}</span>
                {{-- <br> --}}
                العنوان الحالي: <span class="highlight">{{ $record->employee_address }}</span> العنوان الدائم: <span class="highlight">{{ $record->employee_permanent_address }}</span>
            </div>
        </div>
    </div>

    <!-- Preamble -->
    <div class="preamble no-break">
        {{-- <span class="text-bold">تمهيد:</span> --}}
        <div class="content-text">
            @if($contents['preamble_content'] ?? null)
                {!! $contents['preamble_content'] !!}
            @else
                حيث التقت إرادة الطرفين في العمل والتعاون بينهما، تم الإيجاب والقبول واتفقا وهما بكامل الأوصاف المعتبرة شرعا على التالي:
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <!-- Clause 1: Contract Subject -->
    <div class="clause no-break">
        <div class="clause-title">المادة /1/: بيانات الوظيفة:</div>
        <div class="clause-content content-text">
            @if($contents['job_desc'] ?? null)
                {!! $contents['job_desc'] !!}
            @else
                يلتزم الطرف الثاني بأن يعمل لدى الطرف الأول كما يلي: <br/>
                الوظيفة: {{ $record->job_title }}
                @if($record->department)
                في قسم: {{ $record->department }}
                @endif
                @if($record->job_description)
                مقرّ العمل: {{ $record->job_description }}
                @endif
                <br/>
                ، ويكون مسؤولاً عن تنفيذ جميع المهام والواجبات المطلوبة منه بموجب هذا العقد.
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <div class="clause no-break">
        <div class="clause-title">المادة /2/: مدّة العقد:</div>
        <div class="clause-content content-text">
            @if($contents['con_dur'] ?? null)
                {!! $contents['con_dur'] !!}
            @else
               اتفق الطرفان على أن هذا العقد يحكم العلاقة بين كل منهما لمدة سنة بالتقويم الميلادي تبدأ من تاريخ مباشرة الطرف الثاني العمل لدى الطرف الأول، ولا يعتبر هذا العقد ساري المفعول إلا بعد مباشرة الطرف الثاني العمل في مواقع الطرف الأول.
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <div class="clause no-break">
        <div class="clause-title">المادة /3/: فترة التجربة:</div>
        <div class="clause-content content-text">
            @if($contents['test_dur'] ?? null)
                {!! $contents['test_dur'] !!}
            @else
                يكون الطرف الثاني تحت التجربة لمدة (3) ثلاثة شهور تبدأ من تاريخ مباشرته العمل الفعلي وللطرف الأول الحق في فسخ العقد خلال فترة التجربة دون إعلان أو مكافأة أو تعويض وذلك بموجب نظام العمل.
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <div class="clause no-break">
        <div class="clause-title">المادة /4/: التاريخ المحدّد لمباشرة العمل:</div>
        <div class="clause-content content-text">
            @if($contents['start_date'] ?? null)
                {!! $contents['start_date'] !!}
            @else
                يلتزم الطرف الثاني مباشرة العمل خلال فترة أقصاها: 15 يوما اعتبارا من تاريخ توقيع هذا العقد وإلا أعتبر هذا العقد مفسوخا من جانبه.
            @endif
        </div>
    </div>

    <div class="divider"></div>


    <div class="clause no-break">
        <div class="clause-title">المادة /5/: الأجر الشهري:</div>
        <div class="clause-content content-text">
            @if($contents['sal_con'] ?? null)
                {!! $contents['sal_con'] !!}
            @else
                @php
                    $currencySymbol = $record->currency->symbol ?? 'ليرة سورية جديدة';
                @endphp
                يلتزم الطرف الأول بأن يدفع للطرف الثاني أجراً شهرياً مقداره
                <span class="highlight">{{ number_format((float) $record->basic_salary, 2) }} {{ $currencySymbol }}</span>
                في نهاية كل شهر وذلك مقابل التزاماته المحددة في هذا العقد والنظام واللوائح التي يصدرها الطرف الأول.
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <div class="clause no-break">
        <div class="clause-title">المادة /6/: الإجازة السنوية:</div>
        <div class="clause-content content-text">
            @if($contents['leave'] ?? null)
                {!! $contents['leave'] !!}
            @else
                يحق للطرف الثاني إجازة سنوية وفق الشروط التالية: <br/>
                1.		مجموع أيام الأجازة السنوية قدرها ( 15 ) أيام بعد إمضائه فترة التجربة.<br/>
                2.		يتولى الطرف الأول تحديد تاريخ بداية الإجازة ونهايتها وفق ما تسمح به ظروف العمل ، وفي جميع الأحوال يتوجب على الطرف الثاني التمتع بأجازته السنوية في الموعد الذي يحدده الطرف الأول وليس له الاحتجاج على ذلك، مع مراعاة أحكام نظام العمل .<br/>
                3.		للطرف الثاني بموافقة الطرف الأول أن يؤجل للسنة التالية أجازته السنوية أو أياماً منها، وليس له التنازل عنها.<br/>
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <div class="clause no-break">
        <div class="clause-title">المادة /7/: العطل الرسمية والإجازات الأخرى:</div>
        <div class="clause-content content-text">
            @if($contents['vacation'] ?? null)
                {!! $contents['vacation'] !!}
            @else
            1. للموظف الحق بالتمتع بإجازة بأجر كامل في كافة أيام العطل الرسمية التي يقرها نظام العمل.<br/>
            2. يستحق الموظف إجازة بأجر كامل على النحو التالي:<br/>
                •	ثلاثة أيام في حالة زواجه.<br/>
                •	ثلاثة أيام في حالة وفاة أحد فروعه أو أصوله من الدرجة الأولى.<br/>
                •	ثلاثة أيام في حالة ولادة مولود له.<br/>
            3. يعطى الطرف الثاني إذا ثبت مرضه بموجب تقرير طبي صادر من الجهة المعتمدة لدى الطرف الأول إجازة مرضية بأجر كامل عن الثلاثين يوماً الأولى، وبثلاثة أرباع الأجر عن الستين يوماً التالية خلال السنة الواحدة، وفي جميع الأحوال يجوز للطرف الأول التحقق من صحة التقرير الطبي المقدم وإجراء الفحوصات الطبية التي يراها لإثبات اللياقة الطبية للطرف الثاني.<br/>
            4. يجوز للطرف الثاني الحصول على إجازة بدون أجر لمدة ثلاثين يوم في السنة بشرط موافقة الطرف الأول.
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <div class="clause no-break">
        <div class="clause-title">المادة /8/: العمل الإضافي:</div>
        <div class="clause-content content-text">
            @if($contents['overtime'] ?? null)
                {!! $contents['overtime'] !!}
            @else
            1. للشركة الحق في تشغيل من يلزم من موظفيها خلال أيام العطلات الرسمية على أن تدفع له أجرا إضافيا طبقا لأحكام نظام العمل.<br/>
            2. في حالة تكليف الموظف بالعمل خارج أوقات العمل الرسمي يستحق أجراً إضافياً مقداره 30% في الساعة بالإضافة إلى أجرة عن كل ساعة عمل إضافي وذلك حسب نظام العمل.<br/>
            3. لا يحق للطرف الثاني المطالبة بأجر عن ساعات العمل الإضافية إلا إذا كان قد كلف رسمياً بالعمل الإضافي من قبل الطرف الأول ــ ومن خوله هذه الصلاحية ولا يعتد بغير هذا التكلف الرسمي كدليل على ذلك العمل الإضافي.<br/>
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <div class="clause no-break">
        <div class="clause-title">المادة /9/: ساعات العمل:</div>
        <div class="clause-content content-text">
            @if($contents['working_hours'] ?? null)
                {!! $contents['working_hours'] !!}
            @else
            1. يلتزم الطرف الثاني بأن يعمل في خدمة الطرف الأول بمعدل ( 48 ) ساعة أسبوعياً، ولا يدخل في حساب ساعات العمل اليومية الفعلية الفترات المخصصة للراحة والصلاة والطعام، ويعتبر يوم الجمعة راحة أسبوعية للطرف الثاني بأجر كامل.<br/>
            2. يثبت الطرف الثاني حضوره وانصرافه حسب الطريقة التي يحددها الطرف الأول لمتابعة ساعات الدوام.<br/>
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <div class="clause no-break">
        <div class="clause-title">المادة /10/: شروط عامّة:</div>
        <div class="clause-content content-text">
            @if($contents['conditions'] ?? null)
                {!! $contents['conditions'] !!}
            @else
            1. يكون نظام العمل الساري المفعول في سوريا النظام الوحيد الذي يرجع إليه في كل ما يرد به نص في هذا العقد، وكل نزاع ينشأ بخصوص تفسير هذا العقد يكون الفصل فيه للجهة القضائية وفقاً لنظام العمل.<br/>
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
            9. يلتزم الطرف الثاني بالمحافظة على أسرار العمل سواء أثناء فترة خدمته أو بعد انتهائها ولا يحق له خلال سريان هذا العقد أن يعمل لدى الغير بأجر أو بدون أجر في تطوير مخططات مشابهة أو منافسة لمشاريع الشركة، وسواء كان ذلك خلال أو خارج أوقات الدوام الرسمي للطرف الأول، وأن يكرس وقت العمل الرسمي لأداء عمله، وأن يبادر إلى تقديم العون والمساعدة لزملائه في العمل دون أن يشترط لذلك أجرا إضافيا أو مكافأة خاصة، وقد اتفق الطرفان على أن يعتبر إخلال الطرف الثاني بهذا الالتزام إخلالا بالتزام جوهري بعقد العمل الموقع بينهما.
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <div class="clause no-break">
        <div class="clause-title">المادة /11/: تجديد وفسخ العقد ونهاية الخدمة:</div>
        <div class="clause-content content-text">
            @if($contents['renew'] ?? null)
                {!! $contents['renew'] !!}
            @else
            1. يتجدد هذا العقد بين الطرفين بعد انتهاء مدته الأصلية لمدة أخرى مماثلة ، وفي حالة إستمرار الطرفين في تنفيذ هذا العقد بعد التجديد الأول يعتبر العقد مجدداً لفترة غير محددة.<br/>
            2. في جميع الأحوال وفي حالة رغبة أحد الطرفين بعدم تجديد العقد يتوجب على الطرف الذي يرغب في إنهاء العقد إعطاء الطرف الآخر فترة إنذار لا تقل مدتها عن ( 30 ) ثلاثين يوما ويجب أن يكون الإخطار كتابيا ويسلم إلى الطرف الموجه إليه.<br/>
            3. يحق للطرف الأول فسخ العقد بدون مكافأة أو سبق إعلان أو تعويض أو تحمل نفقات في الحالات الوارده في قانون العاملين الموحد في سوريا وهي على النحو التالي::<br/>
 	            • إذا وقع من الطرف الثاني أي اعتداء على الطرف الأول أو من يمثله أثناء العمل أو بسببه.<br/>
 	            • إذا لم يقم الطرف الثاني بتأدية الالتزامات الجوهرية المترتبة عليه،أو لم يطع الأوامر المشروعة، أو لم يراع عمدا التعليمات المبلغ بها من قبل رؤسائه.<br/>
 	            • إذا ثبت أن الطرف الثاني أفشى الأسرار الخاصة بالطرف الأول أو بعملائة.<br/>
 	            • إذا ارتكب الطرف الثاني خطأ عمدا بقصد الحاق خسارة ماديه بالطرف الأول.<br/>
 	            • إذا تغيب الطرف الثاني دون سبب مشروع أكثر من عشرين يوما خلال السنة الواحدة أو أكثر من عشر أيام متتالية.<br/>
 	            • لجوء الطرف الثاني إلى التزوير للحصول على العمل.<br/>
            4. يستحق الموظف مكافأة نهاية الخدمة حسب نظام العمل ويتم إحتسابها على أساس الراتب الأساسي بالإضافة للبدلات النظامية المنصوص عليها في عقد العمل ولا يدخل في ذلك المكافآت والحوافز والعمولات والنسب من أثمان المبيعات والتي تكون بطبيعتها قابلة للزيادة أو النقصان.
            @endif
        </div>
    </div>

    <div class="divider"></div>


    <div class="clause no-break">
        <div class="clause-title">المادة /12/: الاطلاع على اللائحة الداخلية:</div>
        <div class="clause-content content-text">
            @if($contents['system_notes'] ?? null)
                {!! $contents['system_notes'] !!}
            @else
            يقر الطرف الثاني بأنه قد اطلع على لائحة نظام العمل الأساسية ولائحة المكافآت والجزاءات للطرف الأول والمعتمدة من وزارة العمل.
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <div class="clause no-break">
        <div class="clause-title">المادة /13/: عدد نسخ العقد:</div>
        <div class="clause-content content-text">
            @if($contents['no_copies'] ?? null)
                {!! $contents['no_copies'] !!}
            @else
            تم تحرير هذا العقد بمدينة_____________ في يوم ____________ الموافق: ___ / ___ / _____ من نسختين لكل منهما نفس القوة والأثر ويحتفظ كل طرف بنسخة للعمل بموجبها والتقيد بأحكامها.
            @endif
        </div>
    </div>

    <div class="divider"></div>
{{--
    <!-- Clause 1: Contract Subject -->
    <div class="clause no-break">
        <div class="clause-title">البند الأول: موضوع العقد</div>
        <div class="clause-content content-text">
            @if($contents['subject_content'] ?? null)
                {!! $contents['subject_content'] !!}
            @else
                يتعهد الطرف الثاني بالعمل لدى الطرف الأول في وظيفة {{ $record->job_title }}
                @if($record->department)
                في قسم {{ $record->department }}
                @endif
                ، ويكون مسؤولاً عن تنفيذ جميع المهام والواجبات المطلوبة منه بموجب هذا العقد.
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <!-- Clause 2: Responsibilities -->
    <div class="clause no-break">
        <div class="clause-title">البند الثاني: المسؤوليات والواجبات</div>
        <div class="clause-content content-text">
            @if($contents['responsibilities_content'] ?? null)
                {!! $contents['responsibilities_content'] !!}
            @else
                <span class="text-bold">المسؤوليات الرئيسية:</span>
                <ul>
                    <li>تنفيذ المهام والواجبات المطلوبة بدقة وكفاءة.</li>
                    <li>الالتزام بساعات العمل المحددة في هذا العقد.</li>
                    <li>المحافظة على ممتلكات الشركة وأسرارها التجارية.</li>
                    <li>التعاون مع الزملاء والإدارة لتحقيق أهداف الشركة.</li>
                    <li>الالتزام بالسياسات والإجراءات الداخلية للشركة.</li>
                </ul>
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <!-- Clause 3: Working Hours -->
    <div class="clause no-break">
        <div class="clause-title">البند الثالث: ساعات العمل</div>
        <div class="clause-content content-text">
            @if($contents['working_hours_content'] ?? null)
                {!! $contents['working_hours_content'] !!}
            @else
                ساعات العمل: {{ $record->working_hours ?? 'حسب سياسة الشركة' }}
                <br>
                أيام العمل: {{ $record->working_days ?? 'حسب سياسة الشركة' }}
                <br>
                يتم تحديد جدول العمل من قبل الإدارة ويمكن تعديله حسب متطلبات العمل.
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <!-- Clause 4: Salary -->
    <div class="clause no-break">
        <div class="clause-title">البند الرابع: الراتب والمزايا</div>
        <div class="clause-content content-text">
            @if($contents['salary_content'] ?? null)
                {!! $contents['salary_content'] !!}
            @else
                @php
                    $currencySymbol = $record->currency->symbol ?? 'ريال';
                @endphp
                الراتب الأساسي: <span class="highlight">{{ number_format((float) $record->basic_salary, 2) }} {{ $currencySymbol }}</span>
                <br>
                بدل السكن: <span class="highlight">{{ number_format((float) $record->housing_allowance, 2) }} {{ $currencySymbol }}</span>
                <br>
                بدل المواصلات: <span class="highlight">{{ number_format((float) $record->transportation_allowance, 2) }} {{ $currencySymbol }}</span>
                <br>
                بدلات أخرى: <span class="highlight">{{ number_format((float) $record->other_allowances, 2) }} {{ $currencySymbol }}</span>
                <br>
                <strong>الإجمالي: <span class="highlight">{{ number_format((float) $record->total_salary, 2) }} {{ $currencySymbol }}</span></strong>
                <br>
                يتم صرف الراتب في نهاية كل شهر ميلادي.
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <!-- Clause 5: Benefits -->
    <div class="clause no-break">
        <div class="clause-title">البند الخامس: المزايا والحقوق</div>
        <div class="clause-content content-text">
            @if($contents['benefits_content'] ?? null)
                {!! $contents['benefits_content'] !!}
            @else
                <span class="text-bold">المزايا والحقوق:</span>
                <ul>
                    <li>إجازة سنوية مدفوعة الأجر: 30 يوماً سنوياً.</li>
                    <li>إجازة مرضية: 30 يوماً سنوياً.</li>
                    <li>تأمين طبي حسب سياسة الشركة.</li>
                    <li>تذكرة سفر سنوية للموظف.</li>
                    <li>مكافأة نهاية الخدمة حسب قانون العمل.</li>
                </ul>
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <!-- Clause 6: Leave -->
    <div class="clause no-break">
        <div class="clause-title">البند السادس: الإجازات</div>
        <div class="clause-content content-text">
            @if($contents['leave_content'] ?? null)
                {!! $contents['leave_content'] !!}
            @else
                <span class="text-bold">الإجازات:</span>
                <ul>
                    <li>الإجازة السنوية: 30 يوماً بعد سنة من الخدمة.</li>
                    <li>الإجازة المرضية: 30 يوماً في السنة بشهادة طبية.</li>
                    <li>إجازة الأعياد والمناسبات الرسمية حسب قانون العمل.</li>
                    <li>إجازة الأمومة للإناث حسب قانون العمل.</li>
                </ul>
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <!-- Clause 7: Contract Duration -->
    <div class="clause no-break">
        <div class="clause-title">البند السابع: مدة العقد</div>
        <div class="clause-content content-text">
            يبدأ هذا العقد من تاريخ: <span class="highlight">{{ $record->start_date ? $record->start_date->format('d/m/Y') : 'غير محدد' }}</span>
            @if($record->end_date)
            وينتهي بتاريخ: <span class="highlight">{{ $record->end_date->format('d/m/Y') }}</span>
            @endif
            <br>
            فترة التجربة: <span class="highlight">{{ $record->probation_period_days }} يوماً</span>
        </div>
    </div>

    <div class="divider"></div>

    <!-- Clause 8: Termination -->
    <div class="clause no-break">
        <div class="clause-title">البند الثامن: إنهاء العقد</div>
        <div class="clause-content content-text">
            @if($contents['termination_content'] ?? null)
                {!! $contents['termination_content'] !!}
            @else
                يحق لأي من الطرفين إنهاء هذا العقد في الحالات التالية:
                <ul>
                    <li>بإشعار مسبق مدته 30 يوماً.</li>
                    <li>في حال إخلال الطرف الآخر بالشروط الجوهرية للعقد.</li>
                    <li>في حال استقالة الموظف مع إشعار مسبق 30 يوماً.</li>
                    <li>في حال فصل الموظف لأسباب مشروعة.</li>
                </ul>
                في حال الفصل التعسفي، يحق للموظف مكافأة نهاية الخدمة كاملة.
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <!-- Clause 9: Confidentiality -->
    <div class="clause no-break">
        <div class="clause-title">البند التاسع: السرية</div>
        <div class="clause-content content-text">
            @if($contents['confidentiality_content'] ?? null)
                {!! $contents['confidentiality_content'] !!}
            @else
                يلتزم الطرف الثاني بالحفاظ على سرية جميع المعلومات والمستندات والبيانات الخاصة بالشركة، وعدم الإفصاح عنها لأي طرف ثالث سواء أثناء فترة العمل أو بعد انتهائها.
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <!-- Clause 10: General Terms -->
    <div class="clause no-break">
        <div class="clause-title">البند العاشر: أحكام عامة</div>
        <div class="clause-content content-text">
            @if($contents['general_terms_content'] ?? null)
                {!! $contents['general_terms_content'] !!}
            @else
                <ul>
                    <li>هذا العقد يشكل الاتفاق الكامل بين الطرفين ويلغي ما قبله من تفاهمات.</li>
                    <li>أي تعديل على هذا العقد يجب أن يكون خطياً وموقعاً من الطرفين.</li>
                    <li>يخضع هذا العقد لقوانين العمل المعمول بها في الدولة.</li>
                    <li>في حال وجود أي خلاف، يتم حله ودياً أولاً ثم عن طريق المحاكم المختصة.</li>
                </ul>
            @endif
        </div>
    </div>
 --}}
    <!-- Signatures -->
    <div class="signature-section no-break">
        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    <div class="text-bold">توقيع الطرف الأول (صاحب العمل)</div>
                    <div class="signature-line"></div>
                    <div>الاسم: {{ $record->company_name }}</div>
                    <div>التاريخ: {{ $record->contract_date ? $record->contract_date->format('Y-m-d') : 'غير محدد' }}</div>
                </td>

                <td class="signature-cell">
                    <div class="text-bold">توقيع الطرف الثاني (الموظف)</div>
                    <div class="signature-line"></div>
                    <div>الاسم: {{ $record->employee_name }}</div>
                    <div>التاريخ: {{ $record->contract_date ? $record->contract_date->format('Y-m-d') : 'غير محدد' }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- <div class="final-statement">
        حرر هذا العقد من نسختين أصليتين بيد كل طرف نسخة للعمل بموجبها
    </div> --}}
</body>
</html>
