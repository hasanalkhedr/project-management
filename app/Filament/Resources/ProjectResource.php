<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\Pages\ProjectReports;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Illuminate\Support\Collection;
class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['expenses.currency', 'payments.currency']);
    }
    protected static ?string $navigationIcon = 'heroicon-s-document-duplicate';
    protected static ?int $navigationSort = 1;
    public static function getModelLabel(): string
    {
        return __('Project');
    }
    public static function getPluralModelLabel(): string
    {
        return __('Projects');
    }
    public static function getNavigationLabel(): string
    {
        return __('Projects');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->translateLabel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('client_id')
                    ->relationship('client', 'name')
                    ->translateLabel()
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->translateLabel()
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('start_date')
                    ->translateLabel()
                    ->required(),
                Forms\Components\DatePicker::make('end_date')->translateLabel(),
                Forms\Components\Select::make('status')
                    ->options([
                        'planned' => 'مخطط',
                        'in_progress' => 'قيد التنفيذ',
                        'completed' => 'مكتمل',
                        'on_hold' => 'متوقف',
                    ])
                    ->translateLabel()
                    ->required(),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->translateLabel()
                    ->searchable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->sortable()->translateLabel(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()->translateLabel()
                    ->color(fn(string $state): string => match ($state) {
                        'planned' => 'gray',
                        'in_progress' => 'info',
                        'completed' => 'success',
                        'on_hold' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()->translateLabel()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()->translateLabel(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->translateLabel()
                    ->options([
                        'planned' => 'مخطط',
                        'in_progress' => 'قيد التنفيذ',
                        'completed' => 'مكتمل',
                        'on_hold' => 'متوقف',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('')->tooltip(__('View')),
                Tables\Actions\EditAction::make()->label('')->tooltip(__('Edit')),
                Tables\Actions\DeleteAction::make()->label('')->tooltip(__('Delete')),
                Tables\Actions\Action::make('reports')  // Note: Tables\Actions\Action
                    ->translateLabel()
                    ->color('success')
                    ->icon('heroicon-o-chart-bar')
                    ->url(fn(Project $record): string => ProjectReports::getUrl(['record' => $record])),

                // //Add Expense Action
                // Tables\Actions\Action::make('addExpense')
                //     ->translateLabel()
                //     ->color('danger')
                //     ->icon('heroicon-o-banknotes')
                //     ->url(fn(Project $record): string => route('filament.admin.resources.expenses.create', [
                //         'project_id' => $record->id
                //     ])),

                // // Add Payment Action
                // Tables\Actions\Action::make('addPayment')
                //     ->translateLabel()
                //     ->color('primary')
                //     ->icon('heroicon-o-credit-card')
                //     ->url(fn(Project $record): string => route('filament.admin.resources.payments.create', [
                //         'project_id' => $record->id
                //     ])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(__('Project Information'))->collapsible()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('name')->translateLabel()
                                    ->label('Project Name')
                                    ->size(TextEntry\TextEntrySize::Large),
                                TextEntry::make('client.name')->translateLabel()
                                    ->label('Client')
                                    ->size(TextEntry\TextEntrySize::Large),
                                TextEntry::make('status')->translateLabel()
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'active' => 'success',
                                        'completed' => 'primary',
                                        'on_hold' => 'warning',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    }),
                            ]),
                        TextEntry::make('description')->translateLabel()
                            ->columnSpanFull(),
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('start_date')->translateLabel()
                                    ->date(),
                                TextEntry::make('end_date')->translateLabel()
                                    ->date(),
                            ]),
                    ]),

                Section::make(__('Financial Summary by Currency'))->collapsible()
                    ->schema([
                        \Filament\Infolists\Components\ViewEntry::make('currency_summaries')->translateLabel()
                            ->view('filament.infolists.components.currency-summaries')
                            ->viewData([
                                'record' => fn($get) => $get('record'), // Properly access the record
                            ]),
                    ]),

                Section::make(__('Expenses'))
                    ->collapsible()
                    ->schema([
                        RepeatableEntry::make('expenses')->translateLabel()
                            ->label('')
                            ->schema([
                                Grid::make(5)
                                    ->schema([
                                        TextEntry::make('date')->translateLabel()
                                            ->date(),
                                        TextEntry::make('amount')->translateLabel()
                                            ->money(fn($record) => $record->currency->code, divideBy: 100),
                                        TextEntry::make('category')->translateLabel(),
                                        TextEntry::make('supplier')->translateLabel(),
                                        TextEntry::make('invoice_number')->translateLabel(),
                                    ]),
                                TextEntry::make('description')->translateLabel()
                                    ->columnSpanFull()
                                    ->placeholder('No description'),
                            ])
                            ->columns(1),
                    ]),

                Section::make(__('Payments'))
                    ->collapsible()
                    ->schema([
                        RepeatableEntry::make('payments')->translateLabel()
                            ->label('')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextEntry::make('date')->translateLabel()
                                            ->date(),
                                        TextEntry::make('amount')->translateLabel()
                                            ->money(fn($record) => $record->currency->code, divideBy: 100),
                                        TextEntry::make('payment_method')->translateLabel(),
                                        TextEntry::make('reference')->translateLabel(),
                                    ]),
                                TextEntry::make('description')->translateLabel()
                                    ->columnSpanFull()
                                    ->placeholder('No description'),
                            ])
                            ->columns(1),
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
            'reports' => Pages\ProjectReports::route('/{record}/reports'),
            'view' => Pages\ViewProject::route('/{record}'),
        ];
    }
}
