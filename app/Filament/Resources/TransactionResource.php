<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Filament\Resources\TransactionResource\Widgets\TransStats;
use App\Filament\Widgets\TransactionStatsOverview;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    public static function getModelLabel(): string
    {
        return __('Transaction');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Transactions');
    }

    public static function getNavigationLabel(): string
    {
        return __('Transactions');
    }
//to hide from navigation
    public static function shouldRegisterNavigation(): bool
{
    return false;
}
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationIcon = 'heroicon-s-arrow-trending-up';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->translateLabel()
                    ->options([
                        'expense' => __('Expense'),
                        'payment' => __('Payment'),
                    ])
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
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->translateLabel()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->translateLabel()
                    ->colors([
                        'danger' => 'expense',
                        'success' => 'payment',
                    ])
                    ->formatStateUsing(fn(string $state): string => __(ucfirst($state)))
                    ->sortable(),

                Tables\Columns\TextColumn::make('currency.code')
                    ->translateLabel()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->numeric(decimalPlaces: 2)
                    ->translateLabel()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->translateLabel()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->translateLabel()
                    ->options([
                        'expense' => __('Expense'),
                        'payment' => __('Payment'),
                    ]),
                Tables\Filters\SelectFilter::make('currency')
                    ->translateLabel()
                    ->relationship('currency', 'name'),
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->translateLabel(),
                        Forms\Components\DatePicker::make('to')
                            ->translateLabel(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListTransactions::route('/'),
            //'create' => Pages\CreateTransaction::route('/create'),
            //'edit' => Pages\EditTransaction::route('/{record}/edit'),
            'report' => Pages\TransactionReport::route('/report'),
        ];
    }
    public static function getWidgets(): array
    {
        return [
            TransStats::class
        ];
    }

}
