<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KynectKPLResource\Pages;
use App\Filament\Resources\KynectKPLResource\RelationManagers;
use App\Models\KynectKPL;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KynectKPLResource extends Resource
{
    protected static ?string $model = KynectKPL::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListKynectKPLS::route('/'),
            'create' => Pages\CreateKynectKPL::route('/create'),
            'edit' => Pages\EditKynectKPL::route('/{record}/edit'),
        ];
    }
}
