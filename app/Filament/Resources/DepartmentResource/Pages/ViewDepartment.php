<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Resources\DepartmentResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use App\Models\Department;

class ViewDepartment extends ViewRecord
{
    protected static string $resource = DepartmentResource::class;

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
                Components\Section::make('معلومات القسم')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('name_ar')
                                    ->label('اسم القسم (عربي)')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->columnSpanFull(),
                                Components\TextEntry::make('name_en')
                                    ->label('اسم القسم (إنكليزي)')
                                    ->columnSpanFull(),
                                Components\TextEntry::make('code')
                                    ->label('رمز القسم')
                                    ->badge()
                                    ->color('primary'),
                                Components\TextEntry::make('manager.name_ar')
                                    ->label('مدير القسم')
                                    ->placeholder('غير محدد'),
                                Components\TextEntry::make('location')
                                    ->label('الموقع')
                                    ->placeholder('غير محدد'),
                                Components\TextEntry::make('phone')
                                    ->label('رقم الهاتف')
                                    ->icon('heroicon-o-phone')
                                    ->placeholder('غير محدد'),
                            ]),
                    ])
                    ->columns(2),

                Components\Section::make('الإحصائيات')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('budget')
                                    ->label('الميزانية')
                                    ->money('SYP')
                                    ->placeholder('غير محدد'),
                            ]),
                    ])
                    ->columns(3),

                Components\Section::make('ملاحظات إضافية')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Components\TextEntry::make('description')
                            ->label('الوصف')
                            ->markdown()
                            ->columnSpanFull()
                            ->placeholder('لا يوجد وصف'),
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
