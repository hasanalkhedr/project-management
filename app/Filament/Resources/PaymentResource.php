<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    public static function getModelLabel(): string
    {
        return __('Payment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Payments');
    }

    public static function getNavigationLabel(): string
    {
        return __('Payments');
    }
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationIcon = 'heroicon-s-arrow-trending-up';
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(
                request()->has('project_id'),
                fn($query) => $query->where('project_id', request('project_id'))
            );
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('project_id')
                    ->translateLabel()
                    ->default(request('project_id')),
                Forms\Components\Select::make('project_id')
                    ->relationship('project', 'name')
                    ->translateLabel()
                    ->required(),
                Forms\Components\Select::make('currency_id')
                    ->relationship('currency', 'name')
                    ->translateLabel()
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->translateLabel()
                    ->numeric(),
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->translateLabel()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('date')
                    ->translateLabel()
                    ->required(),
                Forms\Components\TextInput::make('payment_method')
                    ->translateLabel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('reference')
                    ->translateLabel()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project.name')
                    ->translateLabel()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency.code')
                    ->translateLabel()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->translateLabel()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->translateLabel()
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->translateLabel()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('project')
                    ->translateLabel()
                    ->relationship('project', 'name'),
                Tables\Filters\SelectFilter::make('currency')
                    ->translateLabel()
                    ->relationship('currency', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPayments::route('/'),
            //'create' => Pages\CreatePayment::route('/create'),
            //'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
