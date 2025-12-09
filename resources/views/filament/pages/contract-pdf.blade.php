<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>عقد اتفاق لتنفيذ أعمال البناء - {{ $record->id }}</title>
    <style>
        @font-face {
            font-family: 'amiri';
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path('fonts/Amiri-Regular.ttf') }}') format('truetype');
        }

        body {
            font-family: 'amiri', sans-serif;
            line-height: 1.5;
            word-spacing: 3px;
            direction: rtl;
            text-align: right;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            margin: 0 0;
            page-break-inside: avoid;
            direction: rtl;
            unicode-bidi: embed;
        }

        th,
        td {
            padding: 8px;
            text-align: right;
            page-break-inside: avoid;
        }

        .header {
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .header-table {
            width: 100%;
        }

        .logo-cell {
            width: 20%;
            vertical-align: middle;
        }

        .logo {
            max-height: 120px;
            width: auto;
            max-width: 200px;
            height: 120px;
            object-fit: contain;
        }

        .title-cell {
            width: 60%;
            text-align: center;
            vertical-align: middle;
        }

        .content-cell {
            vertical-align: bottom;
            text-align: right;
        }

        .company-title {
            font-size: 22px;
            font-weight: bold;
            margin: 0;
        }

        .contract-title {
            font-size: 28px;
            margin: 0;
        }

        .contract-meta {
            font-size: 12px;
            color: #666;
            margin: 0;
        }

        .parties-section {
            margin: 5px 0;
            display: table;
            width: 100%;
        }

        .party-row {
            display: table-row;
            margin-bottom: 8px;
        }

        .party-header {
            display: table-cell;
            padding: 4px 8px;
            font-weight: bold;
            color: #2c3e50;
            width: 180px;
            vertical-align: top;
        }

        .party-details {
            display: table-cell;
            padding: 4px 8px;
            vertical-align: top;
        }

        .preamble {
            background: #f8f9fa;
            padding: 6px;
            border-right: 3px solid #3498db;
            margin: 5px 0;
            text-align: justify;
        }

        .divider {
            border-top: 2px solid #bdc3c7;
            margin: 5px 0;
        }

        .clause {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .clause-title {
            font-weight: bold;
            color: #2c3e50;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .clause-content {
            text-align: justify;
            padding: 0 5px;
        }

        .signature-section {
            margin-top: 5px;
            padding-top: 5px;
            border-top: 2px solid #7f8c8d;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .signature-cell {
            width: 50%;
            text-align: center;
            padding: 10px;
            vertical-align: top;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 3px;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        .stamp-placeholder {
            height: 120px;
            border: 1px dashed #bdc3c7;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #7f8c8d;
            font-size: 11px;
        }

        ul {
            padding-right: 20px;
            margin: 5px 0;
        }

        li {
            margin-bottom: 4px;
            text-align: justify;
        }

        .highlight {
            background-color: #fff3cd;
            padding: 1px 3px;
            border-radius: 2px;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .no-break {
            page-break-inside: avoid;
        }

        .text-bold {
            font-weight: bold;
        }

        .final-statement {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <!-- Header Section -->
    <div class="header">
        <table class="header-table">
            <tr>
                {{-- <td class="content-cell" colspan="2">
                    <div class="company-title">شركة أبراج الريان للمقاولات</div>
                </td> --}}
                <td class="logo-cell" rowspan="4" colspan="2">
                    @if (file_exists(public_path('images/new-logo.png')))
                        <img src="{{ $logo }}" class="logo" alt="شعار الشركة" />
                    @endif
                </td>
            </tr>
            <tr>
                <td class="content-cell">
                    <!-- Empty space -->
                </td>
            </tr>
            <tr>
                <td class="content-cell">
                    <div class="contract-title">عقد اتفاق لتنفيذ أعمال البناء</div>
                </td>
            </tr>
            <tr>
                <td class="content-cell">
                    <div class="contract-meta">
                        العقد رقم: CONTRACT-{{ $record->id }} | التاريخ: {{ $record->contract_date->format('d/m/Y') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- الطرفان في سطرين فقط -->
    <div class="parties-section no-break">
        <div class="party-row">
            <div class="party-header">الطرف الأول (المالك):</div>
            <div class="party-details">
                الاسم: <span class="highlight">{{ $record->owner_name }}</span> - رقم الهوية: <span
                    class="highlight">{{ $record->owner_id_number }}</span> - العنوان: <span
                    class="highlight">{{ $record->owner_address }}</span> - الهاتف: <span
                    class="highlight">{{ $record->owner_phone }}</span>
                {{-- {{ $record->owner_name }} - هوية: {{ $record->owner_id_number }} - هاتف: {{ $record->owner_phone }} - عنوان: {{ Str::limit($record->owner_address, 30) }} --}}
            </div>
        </div>
        <div class="party-row">
            <div class="party-header">الطرف الثاني (المقاول):</div>
            <div class="party-details">
                الاسم/اسم الشركة: <span class="highlight">{{ $record->contractor_name }}</span> - السجل التجاري: <span
                    class="highlight">{{ $record->contractor_commercial_registration }}</span> - العنوان: <span
                    class="highlight">{{ $record->contractor_address }}</span> - الهاتف: <span
                    class="highlight">{{ $record->contractor_phone }}</span>
                {{-- {{ $record->contractor_name }} - سجل: {{ $record->contractor_commercial_registration }} - هاتف: {{ $record->contractor_phone }} - عنوان: {{ Str::limit($record->contractor_address, 30) }} --}}
            </div>
        </div>
    </div>

    <!-- التمهيد -->
    <div class="preamble no-break">
        <span class="text-bold">تمهيد:</span>
        حيث إن الطرف الأول يرغب في تنفيذ أعمال بناء وتشطيب لمشروعه الكائن في {{ $record->project_location }}، وحيث إن
        الطرف الثاني لديه الخبرة والإمكانيات اللازمة لتنفيذ هذه الأعمال، فقد اتفق الطرفان على ما يلي:
    </div>

    <div class="divider"></div>

    <!-- البنود -->
    <div class="clause no-break">
        <div class="clause-title">البند الأول: موضوع العقد</div>
        <div class="clause-content">
            يتعهد الطرف الثاني بتنفيذ جميع الأعمال الخاصة بـ الإنشاء والتشييد والتشطيب والإكساء لمبنى الطرف الأول، وتشمل
            على سبيل المثال لا الحصر:
            <ul>
                <li>أعمال الحفر والأساسات والهيكل الخرساني.</li>
                <li>أعمال البناء واللياسة والدهان.</li>
                <li>أعمال الكهرباء والسباكة والتكييف.</li>
                <li>أعمال الأرضيات، الجدران، الأسقف، الأبواب، النوافذ، الديكور والإكساء الكامل حسب المواصفات.</li>
            </ul>
        </div>
    </div>

    <div class="divider"></div>

    <div class="clause no-break">
        <div class="clause-title">البند الثاني: المواصفات والمخططات</div>
        <div class="clause-content">
            يتم تنفيذ الأعمال طبقاً للمخططات الهندسية والمواصفات الفنية المعتمدة من الطرف الأول أو من المهندس المشرف،
            ويُعد أي تعديل لاحق بموجب ملحق اتفاق خطي موقع من الطرفين.
        </div>
    </div>

    <div class="divider"></div>

    <div class="clause no-break">
        <div class="clause-title">البند الثالث: مدة التنفيذ</div>
        <div class="clause-content">
            مدة تنفيذ المشروع هي (<span class="highlight">{{ $record->execution_period }} يوم</span>) تبدأ من تاريخ
            تسليم الموقع، على أن يلتزم الطرف الثاني بالجدول الزمني المتفق عليه.
            وفي حال التأخير غير المبرر، يحق للطرف الأول فرض غرامة تأخير بنسبة (<span
                class="highlight">{{ $record->delay_penalty_percentage }}%</span>) عن كل يوم تأخير بعد المدة المحددة،
            بحد أقصى (<span class="highlight">{{ $record->max_penalty_percentage }}%</span>) من قيمة العقد.
        </div>
    </div>

    <div class="divider"></div>

    <div class="clause no-break">
        <div class="clause-title">البند الرابع: قيمة العقد وطريقة الدفع</div>
        <div class="clause-content">
            @php
                $currencySymbol = $record->currency->symbol ?? 'ريال';
                $totalValue = number_format($record->total_contract_value, 2);
            @endphp
            قيمة العقد الإجمالية هي مبلغ وقدره (<span class="highlight">{{ $totalValue }}
                {{ $currencySymbol }}</span>) تُدفع على النحو التالي:
            <ul>
                <li>دفعة أولى: عند توقيع العقد بنسبة (<span
                        class="highlight">{{ $record->initial_payment_percentage }}%</span>) من قيمة العقد.</li>
                <li>دفعة ثانية: بعد إنجاز مرحلة الهيكل الخرساني بنسبة (<span
                        class="highlight">{{ $record->concrete_stage_payment_percentage }}%</span>).</li>
                <li>دفعة ثالثة: بعد الانتهاء من أعمال التشطيب بنسبة (<span
                        class="highlight">{{ $record->finishing_stage_payment_percentage }}%</span>).</li>
                <li>دفعة نهائية: بعد التسليم النهائي وخلو المشروع من الملاحظات بنسبة (<span
                        class="highlight">{{ $record->final_payment_percentage }}%</span>).</li>
            </ul>
        </div>
    </div>

    <div class="divider"></div>

    <div class="clause no-break">
        <div class="clause-title">البند الخامس: الالتزامات</div>
        <div class="clause-content">
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
        </div>
    </div>

    <div class="divider"></div>

    <div class="clause no-break">
        <div class="clause-title">البند السادس: الضمان والصيانة</div>
        <div class="clause-content">
            يتعهد الطرف الثاني بضمان الأعمال المنفذة لمدة (<span class="highlight">12 شهراً</span>) من تاريخ التسليم
            النهائي، ضد أي عيب في التنفيذ أو المواد، ويتحمل نفقات الإصلاح خلال هذه المدة.
        </div>
    </div>

    <div class="divider"></div>

    <div class="clause no-break">
        <div class="clause-title">البند السابع: فسخ العقد</div>
        <div class="clause-content">
            يحق للطرف الأول فسخ العقد في الحالات التالية:
            <ul>
                <li>تأخر الطرف الثاني عن تنفيذ الأعمال دون مبرر.</li>
                <li>إخلاله بالشروط أو المواصفات المتفق عليها.</li>
                <li>توقفه عن العمل دون سبب وجيه لأكثر من (15) يوماً.</li>
            </ul>
            وفي حال الفسخ، يُلزم الطرف الثاني بتسليم جميع المواد والأعمال المنفذة حتى تاريخه ودفع أي تعويض يترتب على
            ذلك.
        </div>
    </div>

    <div class="divider"></div>

    <div class="clause no-break">
        <div class="clause-title">البند الثامن: التحكيم وحل النزاعات</div>
        <div class="clause-content">
            في حال حدوث أي خلاف بين الطرفين، يتم حله وديًا، وإن تعذر ذلك يُحال النزاع إلى التحكيم وفق القوانين المعمول
            بها في (<span class="highlight">{{ $record->arbitration_location }}</span>).
        </div>
    </div>

    <div class="divider"></div>

    <div class="clause no-break">
        <div class="clause-title">البند التاسع: أحكام عامة</div>
        <div class="clause-content">
            <ul>
                <li>لا يجوز لأي طرف التنازل عن العقد أو جزء منه دون موافقة الطرف الآخر كتابةً.</li>
                <li>هذا العقد يشكل الاتفاق الكامل بين الطرفين ويلغي ما قبله من تفاهمات شفوية أو كتابية.</li>
                {{-- @if ($record->general_terms)
                <li>{{ $record->general_terms }}</li>
                @endif --}}
            </ul>
            {{-- @if ($record->notes)
            <div style="background: #fff3cd; padding: 8px; border-radius: 5px; margin-top: 8px;">
                <strong>ملاحظات:</strong> {{ $record->notes }}
            </div>
            @endif --}}
        </div>
    </div>

    <!-- التوقيعات -->
    <div class="signature-section no-break">
        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    <div class="text-bold">توقيع الطرف الأول (المالك)</div>
                    <div class="signature-line"></div>
                    <div>الاسم: {{ $record->owner_name }}</div>
                    <div>التاريخ: {{ $record->contract_date->format('Y-m-d') }}</div>
                    <div class="stamp-placeholder">
                        <br /><br /><br /><br /><br /><br /><br />
                    </div>
                </td>

                <td class="signature-cell">
                    <div class="text-bold">توقيع الطرف الثاني (المقاول)</div>
                    <div class="signature-line"></div>
                    <div>الاسم: {{ $record->contractor_name }}</div>
                    <div>التاريخ: {{ $record->contract_date->format('Y-m-d') }}</div>
                    <div class="stamp-placeholder">
                        <br /><br /><br /><br /><br /><br /><br />
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="final-statement">
        حرر هذا العقد من نسختين أصليتين بيد كل طرف نسخة للعمل بموجبها
    </div>

    {{-- <div class="footer">
        <div>شركة أبراج الريان للمقاولات</div>
        <div>https://alrayanrealestate.com/ - © {{ date('Y') }} جميع الحقوق محفوظة</div>
    </div> --}}
</body>

</html>
