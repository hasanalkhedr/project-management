<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\Pages\ProjectReports;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->relationship('client', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date'),
                Forms\Components\Select::make('status')
                    ->options([
                        'planned' => 'مخطط',
                        'in_progress' => 'قيد التنفيذ',
                        'completed' => 'مكتمل',
                        'on_hold' => 'متوقف',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'planned' => 'gray',
                        'in_progress' => 'info',
                        'completed' => 'success',
                        'on_hold' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'planned' => 'مخطط',
                        'in_progress' => 'قيد التنفيذ',
                        'completed' => 'مكتمل',
                        'on_hold' => 'متوقف',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label(''),
                Tables\Actions\EditAction::make()->label(''),
                Tables\Actions\DeleteAction::make()->label(''),
                Tables\Actions\Action::make('reports')  // Note: Tables\Actions\Action
                    ->label('reports')
                    ->color('success')
                    ->icon('heroicon-o-chart-bar')
                    ->url(fn(Project $record): string => ProjectReports::getUrl(['record' => $record])),
                Tables\Actions\Action::make('addExpense')
                    ->label('Add Expense')
                    ->color('danger')
                    ->icon('heroicon-o-banknotes')
                    ->url(fn(Project $record): string => route('filament.admin.resources.expenses.create', [
                        'project_id' => $record->id
                    ])),

                // Add Payment Action
                Tables\Actions\Action::make('addPayment')
                    ->label('Add Payment')
                    ->color('primary')
                    ->icon('heroicon-o-credit-card')
                    ->url(fn(Project $record): string => route('filament.admin.resources.payments.create', [
                        'project_id' => $record->id
                    ])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // public static function infolist(Infolist $infolist): Infolist
    // {
    //     return $infolist
    //         ->schema([
    //             // Project Header
    //             Components\Section::make()
    //                 ->schema([
    //                     Components\Grid::make(3)
    //                         ->schema([
    //                             Components\TextEntry::make('name')
    //                                 ->label('')
    //                                 ->size('xl')
    //                                 ->weight('bold')
    //                                 ->columnSpan(2),
    //                             Components\TextEntry::make('status')
    //                                 ->badge()
    //                                 ->color(fn(string $state): string => match ($state) {
    //                                     'planned' => 'gray',
    //                                     'in_progress' => 'blue',
    //                                     'completed' => 'green',
    //                                     'on_hold' => 'orange',
    //                                 })
    //                                 ->alignEnd(),
    //                         ]),
    //                 ])
    //                 ->extraAttributes(['class' => 'bg-gradient-to-r from-primary-50 to-primary-100 p-6 rounded-lg']),

    //             // Main Content
    //             Components\Grid::make(3)
    //                 ->schema([
    //                     // Left Column - Project Info
    //                     Components\Grid::make(1)
    //                         ->schema([
    //                             Components\Fieldset::make('Project Information')
    //                                 ->schema([
    //                                     Components\TextEntry::make('client.name')
    //                                         ->label('Client')
    //                                         ->icon('heroicon-o-user-circle'),
    //                                     Components\TextEntry::make('description')
    //                                         ->columnSpanFull()
    //                                         ->html()
    //                                         ->prose(),
    //                                     Components\TextEntry::make('start_date')
    //                                         ->label('Timeline')
    //                                         ->formatStateUsing(fn($state, $record) =>
    //                                             $state . ' → ' .
    //                                             ($record->end_date ?? 'Ongoing'))
    //                                         ->icon('heroicon-o-calendar'),
    //                                 ])
    //                                 ->columns(2),

    //                             Components\Fieldset::make('Financial Summary')
    //                                 ->schema([
    //                                     Components\TextEntry::make('total_expenses')
    //                                         ->money(fn($record) => $record->expenses->first()?->currency->code ?? 'USD')
    //                                         ->color('danger')
    //                                         ->icon('heroicon-o-arrow-trending-down'),
    //                                     Components\TextEntry::make('total_payments')
    //                                         ->money(fn($record) => $record->payments->first()?->currency->code ?? 'USD')
    //                                         ->color('success')
    //                                         ->icon('heroicon-o-arrow-trending-up'),
    //                                     Components\TextEntry::make('net_profit')
    //                                         ->money(fn($record) => $record->payments->first()?->currency->code ?? 'USD')
    //                                         ->color(fn($state) => $state >= 0 ? 'success' : 'danger')
    //                                         ->icon(fn($state) => $state >= 0 ? 'heroicon-o-banknotes' : 'heroicon-o-exclamation-circle'),
    //                                 ])
    //                                 ->columns(1),
    //                         ]),

    //                     // Right Column - Tables
    //                     Components\Grid::make(1)
    //                         ->columnSpan(2)
    //                         ->schema([
    //                             // Expenses Section
    //                             Components\Section::make('Expenses')
    //                                 ->schema([
    //                                     Components\TextEntry::make('expenses_count')
    //                                         ->label('Total Expenses')
    //                                         ->state(fn($record) => $record->expenses->count())
    //                                         ->badge()
    //                                         ->color('gray'),

    //                                     Components\RepeatableEntry::make('expenses')
    //                                         ->schema([
    //                                             Components\Grid::make(4)
    //                                                 ->schema([
    //                                                     Components\TextEntry::make('date')
    //                                                         ->date(),
    //                                                     Components\TextEntry::make('description')
    //                                                         ->columnSpan(2),
    //                                                     Components\TextEntry::make('amount')
    //                                                         ->money(fn($record) => $record->currency->code),
    //                                                 ]),
    //                                         ])
    //                                         ->grid(1)
    //                                         ->columnSpanFull(),
    //                                 ])
    //                                 ->collapsible(),

    //                             // Payments Section
    //                             Components\Section::make('Payments')
    //                                 ->schema([
    //                                     Components\TextEntry::make('payments_count')
    //                                         ->label('Total Payments')
    //                                         ->state(fn($record) => $record->payments->count())
    //                                         ->badge()
    //                                         ->color('gray'),

    //                                     Components\RepeatableEntry::make('payments')
    //                                         ->schema([
    //                                             Components\Grid::make(4)
    //                                                 ->schema([
    //                                                     Components\TextEntry::make('date')
    //                                                         ->date(),
    //                                                     Components\TextEntry::make('description')
    //                                                         ->columnSpan(2),
    //                                                     Components\TextEntry::make('amount')
    //                                                         ->money(fn($record) => $record->currency->code),
    //                                                 ]),
    //                                         ])
    //                                         ->grid(1)
    //                                         ->columnSpanFull(),
    //                                 ])
    //                                 ->collapsible(),
    //                         ]),
    //                 ]),
    //         ]);
    // }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Project Header - Styled with Filament methods
                Components\Section::make()
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('name')
                                    ->label('')
                                    ->size(TextEntrySize::Large)
                                    ->weight(FontWeight::ExtraBold)
                                    ->columnSpan(2),
                                Components\TextEntry::make('status')
                                    ->label('')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'planned' => 'gray',
                                        'in_progress' => 'blue',
                                        'completed' => 'green',
                                        'on_hold' => 'orange',
                                        default => 'gray',
                                    })
                                    ->size(TextEntrySize::Large),
                            ]),
                    ])->columns(3),

                // Main Content
                Components\Section::make('')
                    ->schema([
                        // Left Column - Project Info
                        Components\Section::make(1)
                            ->schema([
                                Components\Fieldset::make('Project Information')
                                    ->schema([
                                        Components\TextEntry::make('client.name')
                                            ->label('Client')
                                            ->icon('heroicon-o-user-circle'),

                                        Components\TextEntry::make('start_date')
                                            ->label('Timeline')
                                            ->formatStateUsing(fn($state, $record) =>
                                                \Carbon\Carbon::parse($state)->format('M d, Y') . ' → ' .
                                                ($record->end_date ? \Carbon\Carbon::parse($record->end_date)->format('M d, Y') : 'Ongoing'))
                                            ->icon('heroicon-o-calendar-days'),

                                        Components\TextEntry::make('progress')
                                            ->label('Progress')
                                            ->formatStateUsing(fn($state) => "{$state}%")
                                            ->icon('heroicon-o-chart-bar')
                                            ->color(fn($state) => match (true) {
                                                $state >= 80 => 'success',
                                                $state >= 50 => 'primary',
                                                default => 'warning',
                                            }),

                                        Components\TextEntry::make('description')
                                            ->columnSpanFull()
                                            ->html()
                                            ->prose(),
                                    ])
                                    ->columns(2),

                                Components\Fieldset::make('Financial Summary')
                                    ->schema([
                                        Components\TextEntry::make('budget')
                                            ->money(fn($record) => $record->payments->first()?->currency->code ?? 'USD')
                                            ->icon('heroicon-o-currency-dollar')
                                            ->color('primary'),

                                        Components\TextEntry::make('total_expenses')
                                            ->money(fn($record) => $record->expenses->first()?->currency->code ?? 'USD')
                                            ->color('danger')
                                            ->icon('heroicon-o-arrow-trending-down'),

                                        Components\TextEntry::make('total_payments')
                                            ->money(fn($record) => $record->payments->first()?->currency->code ?? 'USD')
                                            ->color('success')
                                            ->icon('heroicon-o-arrow-trending-up'),

                                        Components\TextEntry::make('net_profit')
                                            ->money(fn($record) => $record->payments->first()?->currency->code ?? 'USD')
                                            ->color(fn($state) => $state >= 0 ? 'success' : 'danger')
                                            ->icon(fn($state) => $state >= 0 ? 'heroicon-o-banknotes' : 'heroicon-o-exclamation-circle')
                                            ->weight(FontWeight::Bold)
                                            ->size(TextEntrySize::Large),
                                    ])
                                    ->columns(1),
                            ]),

                        // Right Column - Tables
                        Components\Section::make(1)
                            ->columnSpan(2)
                            ->schema([
                                // Summary Cards
                                Components\Grid::make(3)
                                    ->schema([
                                        Components\TextEntry::make('milestones_count')
                                            ->label('Milestones')
                                            ->badge()
                                            ->color('blue')
                                            ->icon('heroicon-o-flag'),

                                        Components\TextEntry::make('tasks_count')
                                            ->label('Tasks')
                                            ->badge()
                                            ->color('indigo')
                                            ->icon('heroicon-o-clipboard-document-list'),

                                        Components\TextEntry::make('team_members_count')
                                            ->label('Team Members')
                                            ->badge()
                                            ->color('purple')
                                            ->icon('heroicon-o-users'),
                                    ]),

                                // Expenses Section
                                Components\Section::make('Expenses')
                                    ->schema([
                                        Components\TextEntry::make('expenses_count')
                                            ->label('Total Expenses')
                                            ->state(fn($record) => $record->expenses->count())
                                            ->badge()
                                            ->color('gray')
                                            ->icon('heroicon-o-receipt-percent'),

                                        Components\TextEntry::make('expenses_total')
                                            ->label('Total Amount')
                                            ->state(fn($record) => $record->expenses->sum('amount'))
                                            ->money(fn($record) => $record->expenses->first()?->currency->code ?? 'USD')
                                            ->color('danger')
                                            ->weight(FontWeight::Bold),

                                        Components\RepeatableEntry::make('expenses')
                                            ->schema([
                                                Components\Grid::make(5)
                                                    ->schema([
                                                        Components\TextEntry::make('date')
                                                            ->date('M d, Y')
                                                            ->color('gray'),
                                                        Components\TextEntry::make('category.name')
                                                            ->color('primary')
                                                            ->badge(),
                                                        Components\TextEntry::make('description')
                                                            ->columnSpan(2),
                                                        Components\TextEntry::make('amount')
                                                            ->money(fn($record) => $record->currency->code)
                                                            ->weight(FontWeight::Bold)
                                                            ->alignEnd(),
                                                    ]),
                                            ])
                                            ->grid(1)
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible()
                                    ->collapsed(fn($record) => $record->expenses->count() > 3),

                                // Payments Section
                                Components\Section::make('Payments')
                                    ->schema([
                                        Components\TextEntry::make('payments_count')
                                            ->label('Total Payments')
                                            ->state(fn($record) => $record->payments->count())
                                            ->badge()
                                            ->color('gray')
                                            ->icon('heroicon-o-credit-card'),

                                        Components\TextEntry::make('payments_total')
                                            ->label('Total Amount')
                                            ->state(fn($record) => $record->payments->sum('amount'))
                                            ->money(fn($record) => $record->payments->first()?->currency->code ?? 'USD')
                                            ->color('success')
                                            ->weight(FontWeight::Bold),

                                        Components\RepeatableEntry::make('payments')
                                            ->schema([
                                                Components\Grid::make(5)
                                                    ->schema([
                                                        Components\TextEntry::make('date')
                                                            ->date('M d, Y')
                                                            ->color('gray'),
                                                        Components\TextEntry::make('method')
                                                            ->badge()
                                                            ->color(fn($state) => match ($state) {
                                                                'credit_card' => 'purple',
                                                                'bank_transfer' => 'blue',
                                                                'paypal' => 'indigo',
                                                                default => 'gray',
                                                            }),
                                                        Components\TextEntry::make('description')
                                                            ->columnSpan(2),
                                                        Components\TextEntry::make('amount')
                                                            ->money(fn($record) => $record->currency->code)
                                                            ->weight(FontWeight::Bold)
                                                            ->alignEnd(),
                                                    ]),
                                            ])
                                            ->grid(1)
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible()
                                    ->collapsed(fn($record) => $record->payments->count() > 3),
                            ]),
                    ])
                    ->columns(3),
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
