<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Resources\ExpenseResource\RelationManagers;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-s-arrow-trending-down';
    protected static ?int $navigationSort = 3;
    public static function getModelLabel(): string
    {
        return __('Expense');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Expenses');
    }

    public static function getNavigationLabel(): string
    {
        return __('Expenses');
    }
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
                    ->default(request('project_id'))
                    ->translateLabel(),
                Forms\Components\Select::make('project_id')
                    ->relationship('project', 'name')
                    ->required()->translateLabel(),
                Forms\Components\Select::make('currency_id')
                    ->relationship('currency', 'name')
                    ->required()->translateLabel(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()->translateLabel(),
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(255)->translateLabel(),
                Forms\Components\DatePicker::make('date')
                    ->required()->translateLabel(),
                Forms\Components\TextInput::make('invoice_number')
                    ->maxLength(255)->translateLabel(),
                Forms\Components\TextInput::make('supplier')
                    ->maxLength(255)->translateLabel(),
                Forms\Components\TextInput::make('category')
                    ->maxLength(255)->translateLabel(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project.name')
                    ->sortable()->translateLabel(),
                Tables\Columns\TextColumn::make('currency.code')
                    ->sortable()->translateLabel(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable()->translateLabel(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()->translateLabel(),
                Tables\Columns\TextColumn::make('date')
                    ->date()->translateLabel()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('project')->translateLabel()
                    ->relationship('project', 'name'),
                Tables\Filters\SelectFilter::make('currency')->translateLabel()
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
