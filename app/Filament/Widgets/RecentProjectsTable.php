<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentProjectsTable extends BaseWidget
{
    protected static ?string $heading = 'Recent Projects';
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Project::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'in_progress' => 'blue',
                        'planned' => 'gray',
                        'completed' => 'green',
                        'on_hold' => 'orange',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),

                // Tables\Columns\Column::make('progress')
                //     ->label('Progress')
                //     ->getStateUsing(fn(Project $record) => $record->getProgressPercentage()) // Ensure this returns a number
                //     ->view('filament.tables.columns.project-progress',[0]),
            ]);
    }
}
