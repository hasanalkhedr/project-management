<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\Pages\SupplierReport;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-s-truck';

    //protected static ?string $navigationGroup = 'Management';

    protected static ?int $navigationSort = 2;
    public static function getModelLabel(): string
    {
        return __('Supplier');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Suppliers');
    }

    public static function getNavigationLabel(): string
    {
        return __('Suppliers');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Supplier Information'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()->translateLabel()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('contact_person')
                            ->maxLength(255)
                            ->translateLabel(),

                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->required()->translateLabel(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('Contact Details'))
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()->translateLabel()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->tel()->translateLabel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('website')
                            ->url()->translateLabel()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('Additional Information'))
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->rows(3)->translateLabel()
                            ->maxLength(65535),

                        Forms\Components\TextInput::make('tax_id')
                            ->label('Tax ID')->translateLabel()
                            ->maxLength(50),

                        Forms\Components\Textarea::make('notes')
                            ->rows(3)->translateLabel()
                            ->maxLength(65535),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()->translateLabel()
                    ->sortable(),

                Tables\Columns\TextColumn::make('contact_person')
                    ->searchable()->translateLabel()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()->translateLabel()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable()->translateLabel()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()->translateLabel()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->translateLabel()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()->translateLabel()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
                // Tables\Actions\RestoreAction::make(),
                // Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\Action::make('reports')  // Note: Tables\Actions\Action
                    ->translateLabel()
                    ->color('success')
                    ->icon('heroicon-o-chart-bar')
                    ->url(fn(Supplier $record): string => SupplierReport::getUrl(['record' => $record])),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                    // Tables\Actions\RestoreBulkAction::make(),
                    // Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //RelationManagers\ExpensesRelationManager::class,
            //RelationManagers\ProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            //'create' => Pages\CreateSupplier::route('/create'),
            //'edit' => Pages\EditSupplier::route('/{record}/edit'),
            'reports' => Pages\SupplierReport::route('/{record}/reports'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
