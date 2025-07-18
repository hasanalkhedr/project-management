<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentProjectsTable extends BaseWidget
{
    //protected static ?string $heading = 'Recent Projects';
    protected function getTableHeading(): string
    {
        return __('Recent Projects');
    }
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Project::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('client.name')
                    ->label(__('Client'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label(__('Status'))
                    ->color(fn(string $state): string => match ($state) {
                        'in_progress' => 'blue',
                        'planned' => 'gray',
                        'completed' => 'green',
                        'on_hold' => 'orange',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->label(__('Start Date'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->label(__('End Date'))
                    ->sortable(),

                // Tables\Columns\Column::make('progress')
                //     ->label('Progress')
                //     ->getStateUsing(fn(Project $record) => $record->getProgressPercentage()) // Ensure this returns a number
                //     ->view('filament.tables.columns.project-progress',[0]),
            ]);
    }
}
