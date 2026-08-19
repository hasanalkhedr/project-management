<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('معلومات الموظف')
                    ->schema([
                        Components\Split::make([
                            Components\ImageEntry::make('photo')
                                ->label('الصورة الشخصية')
                                ->circular()
                                ->size(150)
                                ->defaultImageUrl(url('/images/default-avatar.png'))
                                ->columnSpan(1),
                            Components\Grid::make(2)
                                ->schema([
                                    Components\TextEntry::make('employee_number')
                                        ->label('رقم الموظف')
                                        ->badge()
                                        ->color('primary'),
                                    Components\TextEntry::make('name_ar')
                                        ->label('الاسم الكامل (عربي)')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->columnSpanFull(),
                                    Components\TextEntry::make('name_en')
                                        ->label('الاسم الكامل (إنكليزي)')
                                        ->columnSpanFull(),
                                    Components\TextEntry::make('nationality')
                                        ->label('الجنسية')
                                        ->formatStateUsing(fn ($state) => $state === 'syrian' ? 'سوري' : 'أخرى'),
                                    Components\TextEntry::make('date_of_birth')
                                        ->label('تاريخ الميلاد')
                                        ->date(),
                                    Components\TextEntry::make('gender')
                                        ->label('الجنس')
                                        ->formatStateUsing(fn ($state) => $state === 'male' ? 'ذكر' : 'أنثى'),
                                    Components\TextEntry::make('marital_status')
                                        ->label('الحالة الاجتماعية')
                                        ->formatStateUsing(fn ($state) => match ($state) {
                                            'single' => 'عازب',
                                            'married' => 'متزوج',
                                            'divorced' => 'مطلق',
                                            'widowed' => 'أرمل',
                                            default => $state,
                                        }),
                                    Components\TextEntry::make('national_id')
                                        ->label('الرقم الوطني')
                                        ->copyable(),
                                    Components\TextEntry::make('place_of_registration')
                                        ->label('مكان القيد'),
                                ])
                                ->columnSpan(2),
                        ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Components\Section::make('معلومات التواصل')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('phone')
                                    ->label('رقم الهاتف')
                                    ->icon('heroicon-o-phone')
                                    ->copyable(),
                                Components\TextEntry::make('email')
                                    ->label('البريد الإلكتروني')
                                    ->icon('heroicon-o-envelope')
                                    ->copyable(),
                                Components\TextEntry::make('current_address')
                                    ->label('العنوان الحالي')
                                    ->columnSpanFull(),
                                Components\TextEntry::make('emergency_contact_name')
                                    ->label('شخص للطوارئ'),
                                Components\TextEntry::make('emergency_contact_phone')
                                    ->label('رقم هاتف الطوارئ')
                                    ->icon('heroicon-o-phone'),
                            ]),
                    ])
                    ->columns(2),

                Components\Section::make('معلومات العمل')
                    ->icon('heroicon-o-briefcase')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('department')
                                    ->label('القسم')
                                    ->badge()
                                    ->color('success'),
                                Components\TextEntry::make('job_title')
                                    ->label('المسمى الوظيفي')
                                    ->badge()
                                    ->color('info'),
                                Components\TextEntry::make('directManager.name_ar')
                                    ->label('المدير المباشر')
                                    ->placeholder('لا يوجد'),
                                Components\TextEntry::make('project.name')
                                    ->label('المشروع المرتبط')
                                    ->placeholder('لا يوجد'),
                                Components\TextEntry::make('contract_type')
                                    ->label('نوع العقد')
                                    ->formatStateUsing(fn ($state) => match ($state) {
                                        'full_time' => 'دوام كامل',
                                        'part_time' => 'دوام جزئي',
                                        'daily' => 'يومي',
                                        'contractor' => 'مقاول',
                                        default => $state,
                                    })
                                    ->badge()
                                    ->color('warning'),
                                Components\TextEntry::make('employment_status')
                                    ->label('حالة الموظف')
                                    ->formatStateUsing(fn ($state) => match ($state) {
                                        'active' => 'نشط',
                                        'suspended' => 'موقوف',
                                        'terminated' => 'منتهي',
                                        default => $state,
                                    })
                                    ->badge()
                                    ->color(fn ($state) => match ($state) {
                                        'active' => 'success',
                                        'suspended' => 'warning',
                                        'terminated' => 'danger',
                                        default => 'gray',
                                    }),
                                Components\TextEntry::make('hire_date')
                                    ->label('تاريخ التوظيف')
                                    ->date()
                                    ->icon('heroicon-o-calendar'),
                                Components\TextEntry::make('probation_period')
                                    ->label('فترة التجربة (أيام)')
                                    ->suffix(' يوم'),
                                Components\TextEntry::make('contract_start_date')
                                    ->label('تاريخ بداية العقد')
                                    ->date(),
                                Components\TextEntry::make('contract_end_date')
                                    ->label('تاريخ نهاية العقد')
                                    ->date()
                                    ->color(fn ($record) => $record->contract_end_date && $record->contract_end_date < now() ? 'danger' : 'default'),
                            ]),
                    ])
                    ->columns(2),

                Components\Section::make('الراتب والبدلات')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('basic_salary')
                                    ->label('الراتب الأساسي')
                                    ->money('SYP')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('success'),
                                Components\TextEntry::make('housing_allowance')
                                    ->label('بدل السكن')
                                    ->money('SYP'),
                                Components\TextEntry::make('transportation_allowance')
                                    ->label('بدل المواصلات')
                                    ->money('SYP'),
                                Components\TextEntry::make('other_allowances')
                                    ->label('بدلات أخرى')
                                    ->money('SYP'),
                                Components\TextEntry::make('total_salary')
                                    ->label('إجمالي الراتب')
                                    ->money('SYP')
                                    ->size('xl')
                                    ->weight('bold')
                                    ->color('primary')
                                    ->columnSpanFull(),
                                Components\TextEntry::make('payment_method')
                                    ->label('طريقة الدفع')
                                    ->formatStateUsing(fn ($state) => $state === 'cash' ? 'نقدي' : 'تحويل بنكي')
                                    ->badge()
                                    ->color('info'),
                                Components\TextEntry::make('bank_account_number')
                                    ->label('رقم الحساب البنكي')
                                    ->copyable()
                                    ->placeholder('لا يوجد'),
                                Components\TextEntry::make('bank_name')
                                    ->label('اسم البنك')
                                    ->placeholder('لا يوجد'),
                                Components\TextEntry::make('social_security_number')
                                    ->label('رقم الضمان الاجتماعي')
                                    ->copyable()
                                    ->placeholder('لا يوجد'),
                            ]),
                    ])
                    ->columns(2),

                Components\Section::make('ملاحظات إضافية')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Components\TextEntry::make('notes')
                            ->label('ملاحظات')
                            ->markdown()
                            ->columnSpanFull()
                            ->placeholder('لا توجد ملاحظات'),
                    ])
                    ->columns(1),

                Components\Section::make('معلومات النظام')
                    ->icon('heroicon-o-cog')
                    ->collapsible()
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('created_at')
                                    ->label('تاريخ الإنشاء')
                                    ->dateTime()
                                    ->since(),
                                Components\TextEntry::make('updated_at')
                                    ->label('آخر تحديث')
                                    ->dateTime()
                                    ->since(),
                                Components\TextEntry::make('deleted_at')
                                    ->label('تاريخ الحذف')
                                    ->dateTime()
                                    ->since()
                                    ->placeholder('غير محذوف'),
                            ]),
                    ])
                    ->columns(3),
            ]);
    }
}
