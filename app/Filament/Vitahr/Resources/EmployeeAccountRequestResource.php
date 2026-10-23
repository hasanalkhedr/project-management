<?php

namespace App\Filament\Vitahr\Resources;

use App\Filament\Vitahr\Resources\EmployeeAccountRequestResource\Pages;
use App\Models\Vitahr\EmployeeAccountRequest;
use App\Services\EmployeeAccountRequestReviewService;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeeAccountRequestResource extends Resource
{
    protected static ?string $navigationGroup = 'نظام الحضور والتصراف';
    protected static ?string $model = EmployeeAccountRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('vitahr.employee_registration_requests');
    }

    public static function getModelLabel(): string
    {
        return __('vitahr.employee_registration_request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('vitahr.employee_registration_requests');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('manage employees') ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('vitahr.request_id'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee.employee_number')
                    ->label(__('vitahr.employee_number'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee.emp_name')
                    ->label(__('vitahr.employee_name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('vitahr.requested_email'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('vitahr.status'))
                    ->badge()
                    ->formatStateUsing(
                        fn(string $state): string => match ($state) {
                            EmployeeAccountRequest::STATUS_PENDING => __('vitahr.pending'),
                            EmployeeAccountRequest::STATUS_APPROVED => __('vitahr.approved'),
                            EmployeeAccountRequest::STATUS_REJECTED => __('vitahr.rejected'),
                            default => $state,
                        }
                    )
                    ->color(fn(string $state): string => match ($state) {
                        EmployeeAccountRequest::STATUS_PENDING => 'warning',
                        EmployeeAccountRequest::STATUS_APPROVED => 'success',
                        EmployeeAccountRequest::STATUS_REJECTED => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('vitahr.submitted_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reviewer.name')
                    ->label(__('vitahr.reviewed_by'))
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label(__('vitahr.reviewed_at'))
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('vitahr.status'))
                    ->options([
                        EmployeeAccountRequest::STATUS_PENDING => __('vitahr.pending'),
                        EmployeeAccountRequest::STATUS_APPROVED => __('vitahr.approved'),
                        EmployeeAccountRequest::STATUS_REJECTED => __('vitahr.rejected'),
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label(__('vitahr.approve'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(__('vitahr.approve_employee_registration'))
                    ->modalDescription(
                        __('vitahr.approve_employee_registration_description')
                    )
                    ->visible(
                        fn(EmployeeAccountRequest $record): bool =>
                            $record->status === EmployeeAccountRequest::STATUS_PENDING
                    )
                    ->action(
                        function (EmployeeAccountRequest $record, EmployeeAccountRequestReviewService $service): void {
                            $reviewer = auth()->user();

                            if (!$reviewer instanceof \App\Models\Vitahr\User) {
                                abort(403);
                            }

                            abort_unless(
                                $reviewer->can('manage employees'),
                                403
                            );

                            $service->approve($record->id, $reviewer);
                        }
                    ),

                Tables\Actions\Action::make('reject')
                    ->label(__('vitahr.reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('vitahr.reject_employee_registration'))
                    ->form([
                        \Filament\Forms\Components\Textarea::make('rejection_reason')
                            ->label(__('vitahr.rejection_reason'))
                            ->required()
                            ->maxLength(2000),
                    ])
                    ->visible(
                        fn(EmployeeAccountRequest $record): bool =>
                            $record->status === EmployeeAccountRequest::STATUS_PENDING
                    )
                    ->action(
                        function (EmployeeAccountRequest $record, array $data, EmployeeAccountRequestReviewService $service): void {
                            $reviewer = auth()->user();

                            if (!$reviewer instanceof \App\Models\Vitahr\User) {
                                abort(403);
                            }

                            abort_unless(
                                $reviewer->can('manage employees'),
                                403
                            );

                            $service->reject(
                                $record->id,
                                $reviewer,
                                $data['rejection_reason']
                            );
                        }
                    ),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['employee', 'reviewer']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeeAccountRequests::route('/'),
        ];
    }
}
