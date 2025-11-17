<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Filament\Resources\CurrencyResource\RelationManagers;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationIcon = 'heroicon-s-currency-dollar';
    protected static ?int $navigationSort = 7;
    public static function getModelLabel(): string
    {
        return __('Currency');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Currencies');
    }

    public static function getNavigationLabel(): string
    {
        return __('Currencies');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->translateLabel()
                    ->maxLength(3),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->translateLabel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('symbol')
                    ->translateLabel()
                    ->maxLength(5),
                Forms\Components\TextInput::make('exchange_rate')
                    ->required()
                    ->numeric()
                    ->translateLabel()
                    ->default(1),
                Forms\Components\Toggle::make('is_default')
                    ->translateLabel(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->translateLabel()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->searchable(),
                Tables\Columns\TextColumn::make('symbol')
                    ->translateLabel(),
                Tables\Columns\TextColumn::make('exchange_rate')
                    ->translateLabel()
                    ->numeric(),
                Tables\Columns\IconColumn::make('is_default')
                    ->translateLabel()
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ]);
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
            'index' => Pages\ListCurrencies::route('/'),
            //'create' => Pages\CreateCurrency::route('/create'),
            //'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }
}
