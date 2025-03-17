<?php

namespace App\Filament\Resources\PolicyResource\Pages;

use App\Enums\Gender;
use App\Enums\ImmigrationStatus;
use App\Enums\MaritialStatus;
use App\Enums\UsState;
use App\Filament\Resources\PolicyResource;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;

class EditPolicyContact extends EditRecord
{
    protected static string $resource = PolicyResource::class;

    protected static ?string $navigationLabel = 'Cliente';

    public  function form(Form $form): Form
    {
        return $form
            ->schema([


                Forms\Components\Section::make('Datos del Cliente')
                    ->schema([
                        Forms\Components\Fieldset::make('Datos de Contacto')
                            ->relationship('contact')
                            ->schema([
                                Forms\Components\TextInput::make('first_name')
                                    ->label('Primer Nombre')
                                    ->required(),
                                Forms\Components\TextInput::make('middle_name')
                                    ->label('Segundo Nombre')
                                    ->required(),
                                Forms\Components\TextInput::make('last_name')
                                    ->label('Apellido')
                                    ->required(),
                                Forms\Components\TextInput::make('second_last_name')
                                    ->label('Segundo Apellido')
                                    ->required(),
                                Forms\Components\DatePicker::make('date_of_birth')
                                    ->label('Fecha de Nacimiento'),
                                Forms\Components\TextInput::make('age')
                                    ->label('Edad')
                                    ->disabled()
                                    ->dehydrated(false),
                                Forms\Components\Select::make('gender')
                                    ->label('Genero')
                                    ->options(Gender::class),
                                Forms\Components\Select::make('marital_status')
                                    ->label('Estado Civil')
                                    ->options(MaritialStatus::class),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Telefono'),
                                Forms\Components\TextInput::make('kommo_id')
                                    ->label('Kommo ID'),
                                Forms\Components\TextInput::make('email_address')
                                    ->email()
                                    ->label('Correo Electronico')
                                    ->columnSpan(2),
                            ])->columns(4),

                        Forms\Components\Fieldset::make('Direccion')
                            ->relationship('contact')
                            ->schema([
                                Forms\Components\TextInput::make('address_line_1')
                                    ->label('Direccion 1')
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('address_line_2')
                                    ->label('Direccion 2')
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('zip_code')
                                    ->required()
                                    ->label('Codigo Postal'),
                                Forms\Components\TextInput::make('city')
                                    ->required()
                                    ->label('Ciudad'),
                                Forms\Components\TextInput::make('county')
                                    ->required()
                                    ->label('Condado'),
                                Forms\Components\Select::make('state_province')
                                    ->required()
                                    ->options(UsState::class)
                                    ->label('Estado'),

                            ])->columns(4),

                        Forms\Components\Fieldset::make('Información Migratoria')
                            ->schema([
                                    Forms\Components\Select::make('contact_information.immigration_status')
                                        ->label('Estatus migratorio')
                                        ->options(ImmigrationStatus::class)
                                        ->live(),
                                    Forms\Components\TextInput::make('contact_information.immigration_status_category')
                                        ->label('Descripción')
                                        ->columnSpan(2)
                                        ->disabled(fn (Get $get) => $get('contact_information.immigration_status') != ImmigrationStatus::Other->value)
                                        ->columnSpan(2),
                                    Forms\Components\TextInput::make('contact_information.ssn')
                                        ->label('SSN #'),
                                    Forms\Components\TextInput::make('contact_information.passport')
                                        ->label('Pasaporte'),
                                    Forms\Components\TextInput::make('contact_information.alien_number')
                                        ->label('Alien'),
                                    Forms\Components\TextInput::make('contact_information.work_permit_number')
                                        ->label('Permiso de Trabajo #'),
                                    Forms\Components\DatePicker::make('contact_information.work_permit_emission_date')
                                        ->label('Emisión'),
                                    Forms\Components\DatePicker::make('contact_information.work_permit_expiration_date')
                                        ->label('Vencimiento'),
                                    Forms\Components\TextInput::make('contact_information.green_card_number')
                                        ->label('Green Card #'),
                                    Forms\Components\DatePicker::make('contact_information.green_card_emission_date')
                                        ->label('Emisión'),
                                    Forms\Components\DatePicker::make('contact_information.green_card_expiration_date')
                                        ->label('Vencimiento'),
                                    Forms\Components\TextInput::make('contact_information.driver_license_number')
                                        ->label('Green Card #'),
                                    Forms\Components\DatePicker::make('contact_information.driver_license_emission_date')
                                        ->label('Emisión'),
                                    Forms\Components\DatePicker::make('contact_information.driver_license_expiration_date')
                                        ->label('Vencimiento'),
                                ])->columns(3),

//                        Forms\Components\TextInput::make('country_of_residence')
//                            ->label('Pais de Residencia'),
//                        Forms\Components\TextInput::make('country_of_residence')
//                            ->label('Pais de Residencia'),
//                        Forms\Components\TextInput::make('state_of_residence')
//                        Forms\Components\TextInput::make('country_of_birth')
//                            ->label('Pais de Origen'),
//                        Forms\Components\Select::make('immigration_status')
//                            ->label('Estado de Migración')
//                            ->options(ImmigrationStatus::class),
                    ])
                    ->columns(4),
            ]);

    }

}
