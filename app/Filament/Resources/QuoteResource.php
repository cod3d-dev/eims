<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Filament\Resources\QuoteResource\RelationManagers;
use App\Models\Quote;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('contact_id')
                    ->relationship('contact', 'id')
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\TextInput::make('policy_id')
                    ->numeric(),
                Forms\Components\Select::make('insurance_company_id')
                    ->relationship('insuranceCompany', 'name'),
                Forms\Components\TextInput::make('insurance_account_id')
                    ->numeric(),
                Forms\Components\Select::make('policy_type_id')
                    ->relationship('policyType', 'name')
                    ->required(),
                Forms\Components\TextInput::make('premium_amount')
                    ->numeric(),
                Forms\Components\TextInput::make('coverage_amount')
                    ->numeric(),
                Forms\Components\TextInput::make('year')
                    ->numeric(),
                Forms\Components\TextInput::make('main_applicant'),
                Forms\Components\TextInput::make('additional_applicants'),
                Forms\Components\TextInput::make('total_family_members')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('total_applicants')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('estimated_household_income')
                    ->numeric(),
                Forms\Components\TextInput::make('preferred_doctor')
                    ->maxLength(255),
                Forms\Components\TextInput::make('prescription_drugs'),
                Forms\Components\DatePicker::make('start_date'),
                Forms\Components\DatePicker::make('end_date'),
                Forms\Components\DatePicker::make('valid_until'),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('pending'),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contact.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('policy_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('insuranceCompany.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('insurance_account_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('policyType.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('premium_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('coverage_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_family_members')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_applicants')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimated_household_income')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('preferred_doctor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('valid_until')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
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
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
        ];
    }
}
