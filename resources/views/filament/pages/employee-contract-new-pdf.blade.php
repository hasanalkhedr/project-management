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
    <style>
        @font-face {
            font-family: 'almarai';
            font-style: normal;
            font-weight: bold;
            src: url('{{ storage_path('fonts/Almarai-ExtraBold.ttf') }}') format('truetype');
        }

        body {
            font-family: 'almarai', sans-serif;
            color: #1a2b3c;
            margin: 0;
            padding: 0;
        }

        /* ترويسة الصفحة */
        .header-container {
            width: 100%;
            margin-bottom: 20px;
        }

        /* الشعار في أعلى اليسار */
        .logo-wrapper {
            text-align: left;
            margin-bottom: 10px;
        }

        .logo-wrapper img {
            max-height: 75px;
            width: auto;
        }

        /* عنوان العقد بالمنتصف */
        .main-title {
            text-align: center;
            font-size: 30px;
            font-weight: bold;
            color: #0f3d3e;
            margin: 10px 0 10px 0;
        }

        /* صندوق رقم العقد والتاريخ */
        .meta-box {
            border: 1.5px solid #2b4c59;
            border-radius: 8px;
            padding: 3px 3px;
            margin: 0 auto 10px auto;
            width: 95%;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            vertical-align: middle;
            font-size: 14px;
        }

        .meta-right {
            text-align: center;
            width: 45%;
        }

        .meta-divider {
            text-align: center;
            width: 10%;
            color: #7f8c8d;
            font-size: 16px;
        }

        .meta-left {
            text-align: center;
            width: 45%;
        }

        .label-text {
            font-weight: bold;
            color: #1a2b3c;
            margin-left: 8px;
            text-align: center;
        }

        .value-text {
            color: #2c3e50;
            font-weight: bold;
            text-align: center;
        }

        /* الخط الفاصل السفلي */
        .header-line {
            border-bottom: 2px solid #2b4c59;
            margin-bottom: 5px;
            width: 100%;
        }
    </style>
</head>

<body class="contract">
    <!-- Confidentiality Notice -->
    <div style="position: absolute; top: 10px; left: 10px; color: #999; font-size: 12px; font-weight: bold;">
        خاص وسرّي
    </div>

    <div class="header-container">
        <!-- الشعار أعلى اليسار -->
        <div class="logo-cell">
            <img src="{{ $logo }}" class="logo" alt="شعار الشركة" />
        </div>

        <!-- العنوان الرئيسي -->
        <div class="main-title">
            عقد عمل
        </div>

        <!-- صندوق رقم العقد والتاريخ -->
        <div class="meta-box">
            <table class="meta-table">
                <tr>
                    <td class="meta-right">
                        <span class="label-text">رقم العقد:</span>
                        <span class="value-text">EMP-CONTRACT-{{ $record->id }}</span>
                    </td>
                    <td class="meta-divider">|</td>
                    <td class="meta-left">
                        <span class="label-text">التاريخ:</span>
                        <span class="value-text">{{ $record->start_date ? (is_string($record->start_date) ? $record->start_date : $record->start_date->format('d/m/Y')) : 'غير محدد' }}</span>
                    </td>
                    <td class="meta-divider">|</td>
                    <td class="meta-left">
                        <span class="label-text">اليوم:</span>
                        <span class="value-text">{{ $record->start_date ? (is_string($record->start_date) ? \Carbon\Carbon::parse($record->start_date)->locale('ar')->dayName : $record->start_date->locale('ar')->dayName) : 'غير محدد' }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- الخط الفاصل -->
        <div class="header-line"></div>
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
                السيد: <span class="highlight">{{ $record->employee ? $record->employee->name_ar : 'غير محدد' }}</span> من الجنسية: <span class="highlight">{{ $record->employee ? $record->employee->nationality : 'غير محدد' }}</span>
                {{-- <br> --}}
                الرقم الوطني: <span class="highlight">{{ $record->employee ? $record->employee->national_id : 'غير محدد' }}</span> رقم الهوية: <span class="highlight">{{ $record->employee_id_issue_number ?? 'غير محدد' }}</span>
                {{-- <br> --}}
                تاريخ الاصدار: <span class="highlight">{{ $record->employee_id_issue_date ?? 'غير محدد' }}</span> مكان الاصدار: <span class="highlight">{{ $record->employee_id_issue_place ?? 'غير محدد' }}</span>
                {{-- <br> --}}
                رقم الهاتف: <span class="highlight">{{ $record->employee ? $record->employee->phone : 'غير محدد' }}</span> البريد الإلكتروني E-Mail: <span class="highlight">{{ $record->employee ? $record->employee->email : 'غير محدد' }}</span>
                {{-- <br> --}}
                العنوان الحالي: <span class="highlight">{{ $record->employee ? $record->employee->current_address : 'غير محدد' }}</span> العنوان الدائم: <span class="highlight">{{ $record->employee_permanent_address ?? 'غير محدد' }}</span>
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
                الوظيفة: {{ $record->employee && $record->employee->jobTitle ? $record->employee->jobTitle->name_ar : 'غير محدد' }}
                @if($record->employee && $record->employee->department)
                في قسم: {{ $record->employee->department->name_ar }}
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
                    $currencySymbol =  'ليرة سورية جديدة';
                    $basicSalary = $record->employee ? $record->employee->basic_salary : 0;
                    $totalSalary = $record->employee ? $record->employee->total_salary : 0;
                @endphp
                يلتزم الطرف الأول بأن يدفع للطرف الثاني أجراً شهرياً مقداره
                <span class="highlight">{{ number_format((float) $basicSalary, 2) }} {{ $currencySymbol }}</span>
                                بما يعادل مبلغاً قدره
                <span class="highlight">{{ number_format((float) $totalSalary, 2) }} دولار أمريكي</span>
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
    <!-- Signatures -->
    <div class="signature-section no-break">
        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    <div class="text-bold">توقيع الطرف الأول (صاحب العمل)</div>
                    <div class="signature-line"></div>
                    <div>الاسم: {{ $record->company_name }}</div>
                    <div>التاريخ: {{ $record->start_date ? (is_string($record->start_date) ? $record->start_date : $record->start_date->format('Y-m-d')) : 'غير محدد' }}</div>
                </td>

                <td class="signature-cell">
                    <div class="text-bold">توقيع الطرف الثاني (الموظف)</div>
                    <div class="signature-line"></div>
                    <div>الاسم: {{ $record->employee ? $record->employee->name_ar : 'غير محدد' }}</div>
                    <div>التاريخ: {{ $record->start_date ? (is_string($record->start_date) ? $record->start_date : $record->start_date->format('Y-m-d')) : 'غير محدد' }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- <div class="final-statement">
        حرر هذا العقد من نسختين أصليتين بيد كل طرف نسخة للعمل بموجبها
    </div> --}}
</body>
</html>
