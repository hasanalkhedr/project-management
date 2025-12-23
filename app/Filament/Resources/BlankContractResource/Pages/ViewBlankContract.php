<?php

namespace App\Filament\Resources\BlankContractResource\Pages;

use App\Filament\Resources\BlankContractResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;

class ViewBlankContract extends ViewRecord
{
    protected static string $resource = BlankContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->label('تعديل'),
        ];
    }
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('title')
                    ->label('عنوان العقد')
                    ->weight(FontWeight::ExtraBold),

                TextEntry::make('contents')
                    ->label('مضمون العقد')
                    ->html()
                    ->prose() // Adds Tailwind typography styles
                    ->columnSpanFull(),
            ]);
    }
}
