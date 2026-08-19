<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobTitleResource\Pages;
use App\Filament\Resources\JobTitleResource\RelationManagers;
use App\Models\JobTitle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JobTitleResource extends Resource
{
    protected static ?string $model = JobTitle::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'المسميات الوظيفية';

    protected static ?string $modelLabel = 'مسمى وظيفي';

    protected static ?string $pluralModelLabel = 'المسميات الوظيفية';

    protected static ?string $navigationGroup = 'إدارة الموارد البشرية';

    protected static ?int $navigationSort = 23;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name_ar')
                            ->label('المسمى الوظيفي (عربي)')
                            ->required()
                            ->maxLength(255)
                            ->unique(),
                        Forms\Components\TextInput::make('name_en')
                            ->label('المسمى الوظيفي (إنكليزي)')
                            ->required()
                            ->maxLength(255)
                            ->unique(),
                        Forms\Components\Select::make('department_id')
                            ->label('القسم')
                            ->relationship('department', 'name_ar')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_ar')
                    ->label('المسمى (عربي)')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name_en')
                    ->label('المسمى (إنكليزي)')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('department.name_ar')
                    ->label('القسم')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('الوصف')
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('employees_count')
                    ->label('عدد الموظفين')
                    ->counts('employees')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department')
                    ->label('القسم')
                    ->relationship('department', 'name_ar'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListJobTitles::route('/'),
            'create' => Pages\CreateJobTitle::route('/create'),
            'edit' => Pages\EditJobTitle::route('/{record}/edit'),
        ];
    }
}
