<?php

namespace App\Filament\Resources;

use App\Filament\Actions\ExportBlankContractToPdfAction;
use App\Models\BlankContract;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BlankContractResource extends Resource
{
    protected static ?string $model = BlankContract::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'نماذج العقود';

    protected static ?string $modelLabel = 'نموذج عقد';

    protected static ?string $pluralModelLabel = 'نماذج العقود';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('عنوان العقد')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\RichEditor::make('contents')
                    ->label('مضمون العقد')
                    ->placeholder('يمكنك إضافة محتويات العقد هنا وتنسيقها بالطريقة التي تراها مناسبة (كما يمكن إضافة الصور)')
                    ->required()
                    ->toolbarButtons([
                        'attachFiles',
                        'blockquote',
                        'bold',
                        'bulletList',
                        'codeBlock',
                        'h1',
                        'h2',
                        'h3',
                        'italic',
                        'link',
                        'orderedList',
                        'redo',
                        'strike',
                        'underline',
                        'undo',
                        'alignCenter',
                        'alignLeft',
                        'alignRight',

                    ])
                    ->fileAttachmentsDisk('public') // or 'public', 's3', etc.
                    ->fileAttachmentsDirectory('contracts/attachments')
                    ->fileAttachmentsVisibility('private')
                    ->columnSpanFull()
                    ->extraInputAttributes(['style' => 'min-height: 800px;']), // Optional: set min height
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('رقم العقد')
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان العقد')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخر تعديل')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('عرض'),
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
                ExportBlankContractToPdfAction::make(), // إضافة زر التصدير

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('إنشاء نموذج عقد جديد'),
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
            'index' => \App\Filament\Resources\BlankContractResource\Pages\ListBlankContracts::route('/'),
            'create' => \App\Filament\Resources\BlankContractResource\Pages\CreateBlankContract::route('/create'),
            'view' => \App\Filament\Resources\BlankContractResource\Pages\ViewBlankContract::route('/{record}'),
            'edit' => \App\Filament\Resources\BlankContractResource\Pages\EditBlankContract::route('/{record}/edit'),
        ];
    }
}
