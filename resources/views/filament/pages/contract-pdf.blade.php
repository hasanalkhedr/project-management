<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>عقد اتفاق لتنفيذ أعمال البناء - {{ $record->id }}</title>
    <link rel="stylesheet" href="{{public_path('css/reports.css')}}">
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
            border: 0px solid #2b4c59;
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
    <!-- Header Section -->
    <div class="header-container">
        <!-- الشعار أعلى اليسار -->
        <div class="logo-cell">
            <img src="{{ $logo }}" class="logo" alt="شعار الشركة" />
        </div>
        <!-- العنوان الرئيسي -->
        <div class="main-title">
            عقد اتفاق لتنفيذ أعمال البناء
        </div>
        <!-- صندوق رقم العقد والتاريخ -->
        <div class="meta-box">
            <table class="meta-table">
                <tr>
                    <td class="meta-right">
                        <span class="label-text">رقم العقد:</span>
                        <span class="value-text">CONTRACT-{{ $record->id }}</span>
                    </td>
                    <td class="meta-divider">|</td>
                    <td class="meta-left">
                        <span class="label-text">التاريخ:</span>
                        <span class="value-text">{{ $record->contract_date->format('d/m/Y') }}</span>
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
            <div class="party-header">الطرف الأول (المالك):</div>
            <div class="party-details">
                الاسم: <span class="highlight">{{ $record->owner_name }}</span> - رقم الهوية: <span class="highlight">{{ $record->owner_id_number }}</span> - العنوان: <span class="highlight">{{ $record->owner_address }}</span> - الهاتف: <span class="highlight">{{ $record->owner_phone }}</span>
            </div>
        </div>
        <div class="party-row">
            <div class="party-header">الطرف الثاني (المقاول):</div>
            <div class="party-details">
                الاسم/اسم الشركة: <span class="highlight">{{ $record->contractor_name }}</span> - السجل التجاري: <span class="highlight">{{ $record->contractor_commercial_registration }}</span> - العنوان: <span class="highlight">{{ $record->contractor_address }}</span> - الهاتف: <span class="highlight">{{ $record->contractor_phone }}</span>
            </div>
        </div>
    </div>

    <!-- Preamble -->
    <div class="preamble no-break">
        <span class="text-bold">تمهيد:</span>
        <div class="content-text">
            @if($contents['preamble_content'] ?? null)
                {!! $contents['preamble_content'] !!}
            @else
                حيث إن الطرف الأول يرغب في تنفيذ أعمال بناء وتشطيب لمشروعه الكائن في {{ $record->project_location }}، وحيث إن الطرف الثاني لديه الخبرة والإمكانيات اللازمة لتنفيذ هذه الأعمال، فقد اتفق الطرفان على ما يلي:
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <!-- Clause 1: Contract Subject -->
    <div class="clause no-break">
        <div class="clause-title">البند الأول: موضوع العقد</div>
        <div class="clause-content content-text">
            @if($contents['subject_content'] ?? null)
                {!! $contents['subject_content'] !!}
            @else
                يتعهد الطرف الثاني بتنفيذ جميع الأعمال الخاصة بـ الإنشاء والتشييد والتشطيب والإكساء لمبنى الطرف الأول، وتشمل على سبيل المثال لا الحصر:
                <ul>
                    <li>أعمال الحفر والأساسات والهيكل الخرساني.</li>
                    <li>أعمال البناء واللياسة والدهان.</li>
                    <li>أعمال الكهرباء والسباكة والتكييف.</li>
                    <li>أعمال الأرضيات، الجدران، الأسقف، الأبواب، النوافذ، الديكور والإكساء الكامل حسب المواصفات.</li>
                </ul>
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <!-- Clause 2: Specifications -->
    <div class="clause no-break">
        <div class="clause-title">البند الثاني: المواصفات والمخططات</div>
        <div class="clause-content content-text">
            @if($contents['specifications_content'] ?? null)
                {!! $contents['specifications_content'] !!}
            @else
                يتم تنفيذ الأعمال طبقاً للمخططات الهندسية والمواصفات الفنية المعتمدة من الطرف الأول أو من المهندس المشرف، ويُعد أي تعديل لاحق بموجب ملحق اتفاق خطي موقع من الطرفين.
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <!-- Clause 3: Duration -->
    <div class="clause no-break">
        <div class="clause-title">البند الثالث: مدة التنفيذ</div>
        <div class="clause-content content-text">
            @if($contents['duration_content'] ?? null)
                {!! $contents['duration_content'] !!}
            @else
                مدة تنفيذ المشروع هي ({{ $record->execution_period }} يوم) تبدأ من تاريخ تسليم الموقع، على أن يلتزم الطرف الثاني بالجدول الزمني المتفق عليه.
                وفي حال التأخير غير المبرر، يحق للطرف الأول فرض غرامة تأخير بنسبة ({{ $record->delay_penalty_percentage }}%) عن كل يوم تأخير بعد المدة المحددة، بحد أقصى ({{ $record->max_penalty_percentage }}%) من قيمة العقد.
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <!-- Clause 4: Payment -->
    <div class="clause no-break">
        <div class="clause-title">البند الرابع: قيمة العقد وطريقة الدفع</div>
        <div class="clause-content content-text">
            @if($contents['payment_content'] ?? null)
                {!! $contents['payment_content'] !!}
            @else
                @php
                    $currencySymbol = $record->currency->symbol ?? 'ريال';
                    $totalValue = number_format($record->total_contract_value, 2);
                @endphp
                قيمة العقد الإجمالية هي مبلغ وقدره (<span class="highlight">{{ $totalValue }} {{ $currencySymbol }}</span>) تُدفع على النحو التالي:
                <ul>
                    <li>دفعة أولى: عند توقيع العقد بنسبة ({{ $record->initial_payment_percentage }}%) من قيمة العقد.</li>
                    <li>دفعة ثانية: بعد إنجاز مرحلة الهيكل الخرساني بنسبة ({{ $record->concrete_stage_payment_percentage }}%).</li>
                    <li>دفعة ثالثة: بعد الانتهاء من أعمال التشطيب بنسبة ({{ $record->finishing_stage_payment_percentage }}%).</li>
                    <li>دفعة نهائية: بعد التسليم النهائي وخلو المشروع من الملاحظات بنسبة ({{ $record->final_payment_percentage }}%).</li>
                </ul>
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <!-- Clause 5: Obligations -->
    <div class="clause no-break">
        <div class="clause-title">البند الخامس: الالتزامات</div>
        <div class="clause-content content-text">
            @if($contents['obligations_content'] ?? null)
                {!! $contents['obligations_content'] !!}
            @else
                <span class="text-bold">التزامات الطرف الثاني (المقاول):</span>
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
                </ul>
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <!-- Clause 6: Warranty -->
    <div class="clause no-break">
        <div class="clause-title">البند السادس: الضمان والصيانة</div>
        <div class="clause-content content-text">
            @if($contents['warranty_content'] ?? null)
                {!! $contents['warranty_content'] !!}
            @else
                يتعهد الطرف الثاني بضمان الأعمال المنفذة لمدة (12 شهراً) من تاريخ التسليم النهائي، ضد أي عيب في التنفيذ أو المواد، ويتحمل نفقات الإصلاح خلال هذه المدة.
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <!-- Clause 7: Termination -->
    <div class="clause no-break">
        <div class="clause-title">البند السابع: فسخ العقد</div>
        <div class="clause-content content-text">
            @if($contents['termination_content'] ?? null)
                {!! $contents['termination_content'] !!}
            @else
                يحق للطرف الأول فسخ العقد في الحالات التالية:
                <ul>
                    <li>تأخر الطرف الثاني عن تنفيذ الأعمال دون مبرر.</li>
                    <li>إخلاله بالشروط أو المواصفات المتفق عليها.</li>
                    <li>توقفه عن العمل دون سبب وجيه لأكثر من (15) يوماً.</li>
                </ul>
                وفي حال الفسخ، يُلزم الطرف الثاني بتسليم جميع المواد والأعمال المنفذة حتى تاريخه ودفع أي تعويض يترتب على ذلك.
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <!-- Clause 8: Arbitration -->
    <div class="clause no-break">
        <div class="clause-title">البند الثامن: التحكيم وحل النزاعات</div>
        <div class="clause-content content-text">
            @if($contents['arbitration_content'] ?? null)
                {!! $contents['arbitration_content'] !!}
            @else
                في حال حدوث أي خلاف بين الطرفين، يتم حله وديًا، وإن تعذر ذلك يُحال النزاع إلى التحكيم وفق القوانين المعمول بها في ({{ $record->arbitration_location }}).
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <!-- Clause 9: General Terms -->
    <div class="clause no-break">
        <div class="clause-title">البند التاسع: أحكام عامة</div>
        <div class="clause-content content-text">
            @if($contents['general_terms_content'] ?? null)
                {!! $contents['general_terms_content'] !!}
            @else
                <ul>
                    <li>لا يجوز لأي طرف التنازل عن العقد أو جزء منه دون موافقة الطرف الآخر كتابةً.</li>
                    <li>هذا العقد يشكل الاتفاق الكامل بين الطرفين ويلغي ما قبله من تفاهمات شفوية أو كتابية.</li>
                    <li>يُعد كل من الطرفين مسؤولاً عن التزاماته المنصوص عليها في هذا العقد.</li>
                </ul>
            @endif
        </div>
    </div>

    <!-- Additional Notes -->
    @if($contents['notes_content'] ?? null)
    <div class="divider"></div>
    <div class="clause no-break">
        <div class="clause-title">ملاحظات إضافية</div>
        <div class="clause-content">
            <div class="notes-box">
                {!! $contents['notes_content'] !!}
            </div>
        </div>
    </div>
    @endif

    <!-- Signatures -->
    <div class="signature-section no-break">
        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    <div class="text-bold">توقيع الطرف الأول (المالك)</div>
                    <div class="signature-line"></div>
                    <div>الاسم: {{ $record->owner_name }}</div>
                    <div>التاريخ: {{ $record->contract_date->format('Y-m-d') }}</div>
                    {{-- <div class="stamp-placeholder">
                        (ختم وتوقيع)
                    </div> --}}
                </td>

                <td class="signature-cell">
                    <div class="text-bold">توقيع الطرف الثاني (المقاول)</div>
                    <div class="signature-line"></div>
                    <div>الاسم: {{ $record->contractor_name }}</div>
                    <div>التاريخ: {{ $record->contract_date->format('Y-m-d') }}</div>
                    {{-- <div class="stamp-placeholder">
                        (ختم وتوقيع)
                    </div> --}}
                </td>
            </tr>
        </table>
    </div>

    <div class="final-statement">
        حرر هذا العقد من نسختين أصليتين بيد كل طرف نسخة للعمل بموجبها
    </div>
</body>
</html>
