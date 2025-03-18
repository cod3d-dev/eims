<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PolicyDocumentResource\Pages;
use App\Filament\Resources\PolicyDocumentResource\RelationManagers;
use App\Models\PolicyDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PolicyDocumentResource extends Resource
{
    protected static ?string $model = PolicyDocument::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('policy_id')
                    ->relationship('policy', 'id')
                    ->required(),
                Forms\Components\Select::make('document_type_id')
                    ->relationship('documentType', 'name')
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name'),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->maxLength(255),
                Forms\Components\TextInput::make('file_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('original_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('mime_type')
                    ->maxLength(255),
                Forms\Components\TextInput::make('size')
                    ->numeric(),
                Forms\Components\DatePicker::make('due_date'),
                Forms\Components\DatePicker::make('sent_date'),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('policy.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('documentType.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('file_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('original_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mime_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('size')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sent_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => Pages\ListPolicyDocuments::route('/'),
            'create' => Pages\CreatePolicyDocument::route('/create'),
            'edit' => Pages\EditPolicyDocument::route('/{record}/edit'),
        ];
    }
}
