<?php

namespace App\Filament\Resources;

use App\Enums\Gender;
use App\Enums\MaritialStatus;
use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Contactos';

    protected static ?string $modelLabel = 'Contacto';

    protected static ?string $pluralModelLabel = 'Contactos';

    protected static ?string $recordTitleAttribute = 'full_name';

    // protected static ?string $navigationGroup = 'Polizas';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Basic Information Tab
                // Add a fieldset for the basic information
                Forms\Components\Fieldset::make('basic_information')
                    ->label('Información Basica')
                    ->schema([
                        Forms\Components\DateTimePicker::make('created_at')
                            ->disabled()
                            ->dehydrated(false)
                            ->label('Fecha de Creación')
                            ->displayFormat('d/m/Y'),
                        Forms\Components\Select::make('created_by')
                            ->relationship('creator', 'name')
                            ->required()
                            ->options(function () {
                                return User::pluck('name', 'id');
                            })
                            ->label('Asignado a'),
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->label('Nombre'),
                        Forms\Components\TextInput::make('middle_name')
                            ->label('Segundo Nombre'),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->label('Apellido'),
                        Forms\Components\TextInput::make('second_last_name')
                            ->label('Segundo Apellido'),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('date_of_birth')
                                    ->label('Fecha de Nacimiento'),
                                Forms\Components\Select::make('gender')
                                    ->options(Gender::class)
                                    ->label('Género'),
                                Forms\Components\Select::make('marital_status')
                                    ->options(MaritialStatus::class)
                                    ->label('Estado Civil'),
                            ]),
                        Forms\Components\Toggle::make('is_lead')
                            ->label('Prospecto')
                            ->default(true)
                            ->inline(),
                    ]),



                // Create fieldset for contact info

                Forms\Components\Fieldset::make('contact_info')
                    ->label('Información de Contacto')
                    ->schema([
                        Forms\Components\TextInput::make('email_address')
                            ->email()
                            ->label('Correo Electrónico')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->label('Teléfono'),
                        Forms\Components\TextInput::make('phone2')
                            ->tel()
                            ->label('Teléfono 2'),
                        Forms\Components\TextInput::make('whatsapp')
                            ->tel()
                            ->label('WhatsApp'),
                        Forms\Components\TextInput::make('kommo_id')
                            ->label('Kommo ID'),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('preferred_language')
                                    ->label('Idioma Preferido')
                                    ->options([
                                        'spanish' => 'Español',
                                        'english' => 'Inglés',
                                    ])
                                    ->default('spanish'),
                                Forms\Components\Select::make('preferred_contact_method')
                                    ->options([
                                        'email' => 'Correo Electrónico',
                                        'phone' => 'Teléfono',
                                        'sms' => 'SMS',
                                    ])
                                    ->label('Método de Contacto Preferido'),
                                Forms\Components\TimePicker::make('preferred_contact_time')
                                    ->label('Hora de Contacto Preferida')
                                    ->seconds(false),
                            ]),

                    ])->columns(5),

                Forms\Components\Fieldset::make('Datos de Salud')
                    ->label('Datos de Salud')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('weight')
                                    ->numeric()
                                    ->step(0.01)
                                    ->label('Peso'),
                                Forms\Components\TextInput::make('height')
                                    ->numeric()
                                    ->step(0.01)
                                    ->label('Altura'),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Toggle::make('is_eligible_for_coverage')
                                            ->label('Elegible para Cobertura')
                                            ->inline(),
                                    ])
                                    ->compact()
                                    ->hiddenLabel()
                                    ->columnSpan(1),
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Toggle::make('is_tobacco_user')
                                            ->label('Usuario de Tabaco')
                                            ->inline(),
                                    ])
                                    ->compact()
                                    ->hiddenLabel()
                                    ->columnSpan(1),
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Toggle::make('is_pregnant')
                                            ->label('Embarazada')
                                            ->inline(),
                                    ])
                                    ->compact()
                                    ->hiddenLabel()
                                    ->columnSpan(1),
                            ]),
                    ]),



                Forms\Components\Fieldset::make('Otros')
                    ->schema([
                        Forms\Components\TextInput::make('referral_source')
                            ->label('Fuente de Referencia'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Activo',
                                'inactive' => 'Inactivo',
                            ])
                            ->label('Estado')
                            ->default('active')
                            ->columnSpan(1),
                        Forms\Components\Select::make('priority')
                            ->options([
                                'high' => 'Alta',
                                'medium' => 'Media',
                                'low' => 'Baja',
                            ])
                            ->label('Prioridad')
                            ->default('medium')
                            ->columnSpan(1),
                        Forms\Components\DateTimePicker::make('last_contact_date')
                            ->label('Fecha del Último Contacto'),
                        Forms\Components\DateTimePicker::make('next_follow_up_date')
                            ->label('Fecha del Próximo Seguimiento'),

                    ])
                    ->columns(3),







                Forms\Components\Section::make('Información Adicional')
                    ->schema([
                        Forms\Components\Tabs::make('Detalles Adicionales')
                            ->tabs([
                                // Address Tab
                                Forms\Components\Tabs\Tab::make('Dirección')
                                    ->schema([
                                        Forms\Components\Section::make('Dirección Física')
                                            ->schema([
                                                Forms\Components\TextInput::make('address_line_1')
                                                    ->label('Línea 1 de la Dirección'),
                                                Forms\Components\TextInput::make('address_line_2')
                                                    ->label('Línea 2 de la Dirección'),
                                                Forms\Components\Grid::make(4)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('city')
                                                            ->label('Ciudad'),
                                                        Forms\Components\TextInput::make('state_province')
                                                            ->label('Estado o Provincia'),
                                                        Forms\Components\TextInput::make('zip_code')
                                                            ->label('Código Postal'),
                                                        Forms\Components\TextInput::make('county')
                                                            ->label('Condado'),
                                                    ]),
                                            ]),
                                        Forms\Components\Section::make('Dirección de Correo')
                                            ->schema([
                                                Forms\Components\Section::make()
                                                    ->schema([
                                                        Forms\Components\Toggle::make('is_same_as_physical')
                                                            ->label('Es la misma que la Dirección Física')
                                                            ->default(true)
                                                            ->inline(),
                                                    ])
                                                    ->compact()
                                                    ->hiddenLabel(),
                                                Forms\Components\TextInput::make('mailing_address_line_1')
                                                    ->label('Línea 1 de la Dirección de Correo'),
                                                Forms\Components\TextInput::make('mailing_address_line_2')
                                                    ->label('Línea 2 de la Dirección de Correo'),
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('mailing_city')
                                                            ->label('Ciudad de Correo'),
                                                        Forms\Components\TextInput::make('mailing_state_province')
                                                            ->label('Estado o Provincia de Correo'),
                                                        Forms\Components\TextInput::make('mailing_zip_code')
                                                            ->label('Código Postal de Correo'),
                                                    ]),
                                            ]),
                                    ]),

                                // Employment Tab
                                Forms\Components\Tabs\Tab::make('Empleo')
                                    ->schema([
                                        Forms\Components\Section::make('Ingresos Primarios')
                                            ->schema([
                                                Forms\Components\TextInput::make('employer_name_1')
                                                    ->label('Nombre del Empleador'),
                                                Forms\Components\TextInput::make('employer_phone_1')
                                                    ->label('Teléfono del Empleador')
                                                    ->tel(),
                                                Forms\Components\TextInput::make('position_1')
                                                    ->label('Posición'),
                                                Forms\Components\TextInput::make('annual_income_1')
                                                    ->label('Ingresos Anuales')
                                                    ->numeric()
                                                    ->prefix('$'),
                                            ]),
                                        Forms\Components\Section::make('Ingresos Secundarios')
                                            ->schema([
                                                Forms\Components\TextInput::make('employer_name_2')
                                                    ->label('Nombre del Empleador'),
                                                Forms\Components\TextInput::make('employer_phone_2')
                                                    ->label('Teléfono del Empleador')
                                                    ->tel(),
                                                Forms\Components\TextInput::make('position_2')
                                                    ->label('Posición'),
                                                Forms\Components\TextInput::make('annual_income_2')
                                                    ->label('Ingresos Anuales')
                                                    ->numeric()
                                                    ->prefix('$'),
                                            ])
                                            ->collapsed(),
                                        Forms\Components\Section::make('Ingresos Adicionales')
                                            ->schema([
                                                Forms\Components\TextInput::make('employer_name_3')
                                                    ->label('Nombre del Empleador'),
                                                Forms\Components\TextInput::make('employer_phone_3')
                                                    ->label('Teléfono del Empleador')
                                                    ->tel(),
                                                Forms\Components\TextInput::make('position_3')
                                                    ->label('Posición'),
                                                Forms\Components\TextInput::make('annual_income_3')
                                                    ->label('Ingresos Anuales')
                                                    ->numeric()
                                                    ->prefix('$'),
                                            ])
                                            ->collapsed(),
                                    ]),

                                // Immigration Tab
                                Forms\Components\Tabs\Tab::make('Inmigración')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Select::make('immigration_status')
                                                    ->label('Estatus migratorio')
                                                    ->options([
                                                        'citizen' => 'Ciudadano',
                                                        'resident' => 'Residente',
                                                        'asylum_seeker' => 'Solicitando Asilo',
                                                        'ead' => 'Documento de Autorización de Empleo (EAD)',
                                                        'tps' => 'TPS',
                                                        'parole' => 'Parol Humanitario',
                                                        'other' => 'Otro',
                                                    ]),
                                                Forms\Components\TextInput::make('immigration_status_category')
                                                    ->label('Categoría de Estado de Inmigración'),
                                            ]),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('passport_number')
                                                    ->label('Número de Pasaporte'),
                                                Forms\Components\TextInput::make('uscis_number')
                                                    ->label('Número de USCIS'),
                                            ]),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('ssn')
                                                    ->label('Número de Seguro Social'),
                                                Forms\Components\DatePicker::make('ssn_issue_date')
                                                    ->label('Fecha de Emisión del Número de Seguro Social'),
                                            ]),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('green_card_number')
                                                    ->label('Número de Tarjeta Verde'),
                                                Forms\Components\DatePicker::make('green_card_expiration_date')
                                                    ->label('Fecha de Expiración de la Tarjeta Verde'),
                                            ]),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('work_permit_number')
                                                    ->label('Número de Permiso de Trabajo'),
                                                Forms\Components\DatePicker::make('work_permit_expiration_date')
                                                    ->label('Fecha de Expiración del Permiso de Trabajo'),
                                            ]),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('driver_license_number')
                                                    ->label('Número de Licencia de Conducir'),
                                                Forms\Components\DatePicker::make('driver_license_expiration_date')
                                                    ->label('Fecha de Expiración de la Licencia de Conducir'),
                                            ]),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_lead')
                    ->boolean()
                    ->label('Prospecto'),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nombre')
                    ->searchable(['first_name', 'last_name', 'middle_name', 'second_last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('email_address')
                    ->searchable()
                    ->label('Correo Electrónico'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->label('Teléfono')
                    // Add a link to https://ghercys.kommo.com/leads/detail/12788104
                    ->url(fn (Model $record) => 'https://ghercys.kommo.com/leads/detail/' . $record->kommo_id, '_blank'),
                Tables\Columns\TextColumn::make('preferred_language')
                    ->badge()
                    ->label('Idioma Preferido'),
                Tables\Columns\IconColumn::make('is_eligible_for_coverage')
                    ->boolean()
                    ->label('Elegible para Cobertura'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray'
                    })
                    ->label('Estado'),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'high' => 'danger',
                        'medium' => 'warning',
                        'low' => 'success',
                    })
                    ->label('Prioridad'),
                Tables\Columns\TextColumn::make('next_follow_up_date')
                    ->dateTime()
                    ->sortable()
                    ->label('Fecha del Próximo Seguimiento'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Activo',
                        'inactive' => 'Inactivo',
                    ])
                    ->label('Estado'),
                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'high' => 'Alta',
                        'medium' => 'Media',
                        'low' => 'Baja',
                    ])
                    ->label('Prioridad'),
                Tables\Filters\TernaryFilter::make('is_lead')
                    ->label('Estado de Líder'),
                Tables\Filters\TernaryFilter::make('is_eligible_for_coverage')
                    ->label('Elegible para Cobertura'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar Seleccionados'),
                ]),
            ]);
    }



    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }

    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::where('is_lead', true)->count() . ' prospectos';
    // }
}
