<?php

namespace App\Filament\Resources;

use App\Enums\Gender;
use App\Enums\ImmigrationStatus;
use App\Enums\MaritialStatus;
use App\Enums\PolicyType;
use App\Filament\Resources\PolicyResource\Pages;
use App\Filament\Resources\PolicyResource\RelationManagers;
use Faker\Provider\Text;
use Filament\Forms\Components\Actions;
use Filament\Actions\EditAction;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use App\Models\Policy;
use App\Models\Quote;
use App\Models\Contact;
use Illuminate\Contracts\Support\Htmlable;
//use App\Filament\Resources\PolicyResource\RelationManagers\IssuesRelationManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Filament\Notifications\Notification;
use App\Enums\PolicyStatus;
use App\Enums\DocumentStatus;
use App\Enums\RenewalStatus;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Enums\FamilyRelationship;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Enums\FiltersLayout;
use App\Enums\UsState;


class PolicyResource extends Resource
{
    protected static ?string $model = Policy::class;
    protected static ?string $navigationIcon = 'iconoir-privacy-policy';

    protected static ?string $navigationGroup = 'Polizas';
    protected static ?int $navigationSort = 2;


    protected static ?string $navigationLabel = 'Polizas';
    protected static ?string $modelLabel = 'Poliza';
    protected static ?string $pluralModelLabel = 'Polizas';


    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'contact.first_name';

    protected static int $globalSearchResultsLimit = 20;

    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return $record->contact->full_name;
    }

    public static function getGloballySearchableAttributes(): array 
    {
        return ['contact.first_name', 'contact.middle_name', 'contact.last_name', 'contact.second_last_name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Tipo' => $record->policy_type->getLabel() ?? null,
            'Año' => $record->policy_year,
            // Return Pagado if $record->initial_paid is true
            'Estatus' => (string) (($record->initial_paid === true ? 'Pagado' : 'Sin Pagar') . ' / Documentos: ' . ($record->document_status->getLabel())),
            'Cliente Notificado' => ($record->client_notified === true ? 'Sí' : 'No') . ($record->contact->state_province == 'KY' ? ' / ACA: ' . ($record->aca === true ? 'Sí' : 'No') : ''),

        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
//            Pages\ViewPolicy::class,
            Pages\EditPolicy::class,
            Pages\EditPolicyContact::class,
            Pages\EditPolicyApplicants::class,
            Pages\EditPolicyIncome::class,
            Pages\EditPolicyPayments::class,
            Pages\EditPolicyLife::class,
            Pages\ManagePolicyDocument::class,
            Pages\ManagePolicyIssues::class

        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Poliza')
                    ->schema([
                        Forms\Components\Select::make('contact_id')
                            ->label('Cliente')
                            ->relationship(
                                name: 'contact',
                                modifyQueryUsing: fn (Builder $query) => $query->orderBy('first_name')->orderBy('middle_name')->orderBy('last_name')->orderBy('second_last_name'),
                            )
                            ->getOptionLabelFromRecordUsing(fn (Model $record): string => "{$record->first_name} {$record->middle_name} {$record->last_name} {$record->second_last_name}")
                            ->searchable([ 'first_name', 'middle_name', 'last_name', 'second_last_name' ])
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('first_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('middle_name')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('last_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('second_last_name')
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Select::make('agent_id')
                            ->relationship('agent', 'name')
                            ->label('Cuenta'),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Asistente'),
                        Forms\Components\Select::make('policy_type')
                            ->options(PolicyType::class)
                            ->label('Tipo de Poliza'),

                        Forms\Components\Select::make('insurance_company_id')
                            ->relationship('insuranceCompany', 'name')
                            ->preload()
                            ->label('Aseguradora')
                            ->searchable(),
                        Forms\Components\Select::make('policy_year')
                            ->label('Año')
                            ->options(function (Get $get) {
                                $year = Carbon::now()->year;
                                return range($year - 5, $year + 5);
                            })
                            ->default(Carbon::now()->year),
                        Forms\Components\DatePicker::make('effective_date')
                            ->label('Efectiva Desde'),
                        Forms\Components\DatePicker::make('expiration_date')
                            ->label('Vencimiento'),
                        Forms\Components\Grid::make('')
                            ->schema([
                                Forms\Components\TextInput::make('policy_plan')
                                    ->label('Plan'),
                                Forms\Components\TextInput::make('policy_total_cost')
                                    ->label('Costo Poliza'),
                                Forms\Components\TextInput::make('policy_total_subsidy')
                                    ->label('Subsidio'),
                                Forms\Components\TextInput::make('premium_amount')
                                    ->label('Prima'),
                                Forms\Components\TextInput::make('policy_us_county')
                                    ->label('Condado'),
                                Forms\Components\Select::make('policy_us_state')
                                    ->label('Estado')
                                    ->live()
                                    ->options(UsState::class),
                                Forms\Components\TextInput::make('kynect_case_number')
                                    ->label('Caso Kynect'),
                                Forms\Components\Toggle::make('has_existing_kynect_case')
                                    ->inline(false)
                                    ->label('Pedir Caso Kynect'),
                                ])->columns(4)->columnSpanFull(),

                        Forms\Components\Fieldset::make()
                            ->schema([
                                Forms\Components\Select::make('previous_year_policy_user_id')
                                    ->relationship('previousYearPolicyUser', 'name')
                                    ->label('Asistente Año Anterior')
                                    ->columnStart(7)
                                    ->columnSpan(2),
                                Forms\Components\Select::make('status')
                                    ->label('Estatus')
                                    ->columnSpan(2)
                                    ->options(PolicyStatus::class),
                                Forms\Components\Select::make('document_status')
                                    ->columnSpan(2)
                                    ->label('Documentos')
                                    ->disabled()
                                    ->default(DocumentStatus::ToAdd)
                                    ->options(DocumentStatus::class),
                                Forms\Components\Toggle::make('client_notified')
                                    ->inline(false)
                                    ->label('Notificado'),
                                Forms\Components\Toggle::make('autopay')
                                    ->inline(false)
                                    ->label('Cotizacion'),
                                Forms\Components\Toggle::make('initial_paid')
                                    ->inline(false)
                                    ->label('Pagada'),
                                Forms\Components\Toggle::make('aca')
                                    ->inline(false)
                                    ->label('ACA')
                                    ->disabled(fn (Forms\Get $get): bool => $get ('policy_us_state') != UsState::KENTUCKY->value),
                                Forms\Components\Toggle::make('is_initial_verification_complete')
                                    ->inline(false)
                                    ->live()
                                    ->columnSpan(2)
                                    ->label('Verificacion Inicial'),
                                Forms\Components\Select::make('initial_verification_performed_by')
                                    ->relationship('initialVerificationPerformedBy', 'name')
                                    ->label('Verificado Por')
                                    ->disabled(fn (Get $get) => $get('is_initial_verification_complete') != true)
                                    ->columnSpan(3),
                                Forms\Components\DatePicker::make('initial_verification_date')
                                    ->label('Fecha Verificacion')
                                    ->disabled(fn (Get $get) => $get('is_initial_verification_complete') != true)
                                    ->columnSpan(3),
                                Forms\Components\Textarea::make('notes')
                                    ->label('Observaciones')
                                    ->rows(6)
                                    ->columnSpanFull(),
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('add_note')
                                        ->label('Agregar Nota')
                                        ->color('info')
                                        ->modalHeading('Agregar Nota')
                                        ->visible(fn (?Policy $record) => $record && $record->exists)
                                        ->form([
                                            Forms\Components\Textarea::make('note')
                                                ->required()
                                                ->rows(3)
                                                ->label('Nota'),
                                        ])
                                        ->modalSubmitActionLabel('Agregar')
                                        ->action(function (Policy $record, array $data, Set $set): void {
                                            $note = Carbon::now()->toDateTimeString() . ' - ' . auth()->user()->name . ":\n" . $data['note'] . "\n\n" ;
                                            $record->notes = $record->notes . $note;
                                            $record->save();

                                            // Refresh the notes field with the updated value
                                            $set('notes', $record->notes);
                                        }),
                                    ])
                                    ->alignEnd()
                                    ->columnSpanFull(),
                                ])
                            ->columns([ 'md' => 8, 'lg' => 8 ])
                            ->columnSpanFull(),
                        ])->columns(['md' => 4, 'lg' => 4]),
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Insurance Account name
                Tables\Columns\TextColumn::make('agent.name')
                    // format getting the first letter of each word in the name in uppercase
                    ->formatStateUsing(fn(string $state): string => Str::acronym($state))
                    ->label('Cuenta')
                    ->badge()
                    // ->tooltip(fn(string $state): string => $state)
                    ->color(fn (string $state): string => match ($state) {
                        'Ghercy Segovia' => 'success',
                        'Maly Carvajal' => 'primary',
                    })
                    ->sortable(),


                Tables\Columns\TextColumn::make('contact.full_name')
                    ->label('Cliente')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $query) use ($search): Builder {
                            // Search in contact fields
                            $query->whereHas('contact', function (Builder $query) use ($search): Builder {
                                return $query->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('middle_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('second_last_name', 'like', "%{$search}%");
                            });
                            
                            // Search in additional applicants JSON field
                            $query->orWhereRaw("JSON_SEARCH(LOWER(additional_applicants), 'one', LOWER(?)) IS NOT NULL", ["%{$search}%"]);
                            
                            return $query;
                        });
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->join('contacts', 'policies.contact_id', '=', 'contacts.id')
                            ->orderBy('contacts.last_name', $direction)
                            ->orderBy('contacts.first_name', $direction)
                            ->select('policies.*');
                    })
                    ->html()
                    ->formatStateUsing(function(string $state, Policy $record): string { 
                        $customers = $state;

                        foreach ($record->additional_applicants as $applicant) {
                            $customers .= '<br><span class="text-gray-500 text-xs">' . $applicant->first_name . ' ' . $applicant->middle_name . ' ' . $applicant->last_name . ' ' . $applicant->second_last_name . '</span>';
                        }

                        return $customers;
                    }),
                Tables\Columns\TextColumn::make('policy_type')
                    ->badge()
                    ->label('Tipo')
                    ->sortable(),
                Tables\Columns\TextColumn::make('insuranceCompany.code')
                    ->label('Empresa')
                    ->sortable()
                    ->badge()
                    ->tooltip(fn(Policy $record): string => $record->insuranceCompany->name)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('insuranceCompany', function (Builder $query) use ($search): Builder {
                            return $query->where('name', 'like', "%{$search}%");
                        });
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('d-m-Y')
                    ->label('Creada')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Asistente')
                    ->formatStateUsing(fn(string $state): string => Str::acronym($state))
//                    ->tooltip(fn(string $state): string => $state)
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Carlos Rojas' => 'success',
                        'Ricardo Segovia' => 'primary',
                        'Omar Ostos' => 'warning',
                        'Fhiona Segovia' => 'danger',
                        'Ghercy Segovia' => 'info',
                        'Christell' => 'gray',
                        'Raul Medrano' => 'purple',
                    })
                    ->toggleable(),
                Tables\Columns\ColumnGroup::make('Estatus', [
                    Tables\Columns\IconColumn::make('client_notified')
                        ->boolean()
                        ->label('Informado')
                        ->sortable(),
                    Tables\Columns\IconColumn::make('autopay')
                        ->boolean()
                        ->label('Autopay')
                        ->sortable(),
                    Tables\Columns\IconColumn::make('initial_paid')
                        ->boolean()
                        ->label('Inicial')
                        ->sortable(),
                    Tables\Columns\IconColumn::make('aca')
//                        ->boolean()
//                        ->formatStateUsing(fn(Policy $record): bool => ($record->policy_us_state === 'KY' && $record->aca) ? true : false)
//                        ->sortable(),
                        ->label('ACA'),
//                        ->icon(function (Policy $record): string {
////                            dd($record->policy_us_state);
//                            if($record->policy_us_state->value === 'KY' && $record->insuranceCompany->name != 'CareSource' && $record->insuranceCompany->name != 'Anthem') {
//                                if ($record->aca) {
//                                    return 'heroicon-o-check-circle';
//                                } else {
//                                    return 'heroicon-o-x-circle';
//                                }
//                            } else {
//                                return 'heroicon-o-minus';
//                            }
//                        }),
                    Tables\Columns\TextColumn::make('document_status')
                        ->label('Documentos')
                        ->sortable()
                        ->badge()
                        ->action(
                            Tables\Actions\Action::make('viewPendingDocuments')
                                ->label('Ver Documentos Pendientes')
                                ->icon('heroicon-m-document-text')
                                ->modalHeading('Documentos Pendientes')
                                ->modalDescription(fn (Policy $record): string => "Documentos pendientes para la póliza #{$record->id}")
                                ->modalContent(fn (Policy $record): View => view(
                                    'filament.resources.policy-resource.pending-documents',
                                    ['documents' => $record->documents()->where('status', DocumentStatus::Pending)->get()]
                                ))
                                ->modalWidth(MaxWidth::Medium)
                        ),
                ]),
                Tables\Columns\TextColumn::make('effective_date')
                    ->label('Fecha de inicio')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Válida hasta')
                    ->date('d-m-Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_renewal')
                    ->label('Renovada')
                    ->boolean(),
                // Tables\Columns\TextColumn::make('renewal_status')
                //     ->label('Estado de Renovación')
                //     ->badge()
                //     ->formatStateUsing(fn (RenewalStatus $state): string => $state->label())
                //     ->color(fn ($state): string => match ($state->value) {
                //         RenewalStatus::PENDING->value => 'warning',
                //         RenewalStatus::COMPLETED->value => 'success',
                //         RenewalStatus::CANCELLED->value => 'danger',
                //         default => 'gray',
                //     }),
            ])
            ->filters([
               Tables\Filters\SelectFilter::make('agent.name')
                   ->label('Agente')
                   ->relationship('agent', 'name'),
               Tables\Filters\SelectFilter::make('policy_year')
                   ->label('Año Efectivo')
                   ->options([
                    (date('Y') - 2) => (date('Y') - 2),
                    (date('Y') - 1) => (date('Y') - 1),
                    date('Y') => date('Y'),
                    (date('Y') + 1) => (date('Y') + 1),
                    (date('Y') + 2) => (date('Y') + 2),
                   ]),
               Tables\Filters\SelectFilter::make('status')
                   ->label('Estatus')
                   ->options(PolicyStatus::class),
               Tables\Filters\SelectFilter::make('document_status')
                   ->label('Documentos')
                   ->multiple()
                   ->options(DocumentStatus::class),
               Tables\Filters\SelectFilter::make('insurance_company_id')
                   ->relationship('insuranceCompany', 'name')
                   ->label('Compañía de Seguro')
                   ->searchable()
                   ->preload(),
               Tables\Filters\SelectFilter::make('policy_type')
                   ->options(PolicyType::class)
                   ->label('Tipo de Poliza'),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->actions([
                // Create a Action group with 3 actions
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    // Tables\Actions\Action::make('quickedit')
                    //     ->label('Edición Rápida')
                    //     ->icon('heroicon-o-pencil-square')
                    //     ->url(fn (Policy $record): string => route('filament.app.resources.policies.quickedit', $record)),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ReplicateAction::make()
                        ->label('Duplicar')
                        ->icon('heroicon-o-document-duplicate')
                        ->form([
                            Forms\Components\Select::make('policy_type')
                                ->label('Tipo de Poliza')
                                ->options(PolicyType::class)
                                ->required()
                                ->preload()
                                ->searchable(),
                            Forms\Components\DatePicker::make('start_date')
                                ->label('Fecha de inicio')
                                ->required()
                                ->default(now()),
                            Forms\Components\DatePicker::make('end_date')
                                ->label('Fecha de fin')
                                ->required()
                                ->default(now()->addYear()->subDay()),
                            Forms\Components\Textarea::make('notes')
                                ->label('Notas')
                                ->rows(3),
                        ])
                        ->modalHeading('Duplicar Póliza')
                        ->modalDescription('Se creará una nueva póliza con los datos de la póliza actual. Por favor, seleccione el tipo de póliza y especifique las nuevas fechas.')
                        ->modalSubmitActionLabel('Duplicar')
                        ->mutateRecordDataUsing(function (array $data): array {
                            // Remove specific fields that should not be duplicated
                            unset($data['policy_id']);
                            unset($data['number']);
                            unset($data['renewed_from_policy_id']);
                            unset($data['renewed_to_policy_id']);
                            unset($data['renewed_by']);
                            unset($data['renewed_at']);
                            
                            return $data;
                        })
                        ->beforeReplicaSaved(function (Policy $replica, array $data): void {
                            // Update with form data
                            $replica->policy_type = $data['policy_type'];
                            $replica->start_date = $data['start_date'];
                            $replica->end_date = $data['end_date'];
                            $replica->notes = ($replica->notes ? $replica->notes . "\n\n" : '') . 
                                "=== Notas de Duplicación ===\n" . ($data['notes'] ?? 'Póliza duplicada el ' . now()->format('Y-m-d H:i:s'));
                        })
                        ->afterReplicaSaved(function (Policy $replica): void {
                            Notification::make()
                                ->title('Póliza duplicada exitosamente')
                                ->success()
                                ->send();
                        }),
                ]),
                Tables\Actions\Action::make('renew')
                    ->label('Renovar')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Fecha de inicio')
                            ->required()
                            ->default(fn (Policy $record) => $record->getRenewalPeriod()['start_date']),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Fecha de fin')
                            ->required()
                            ->default(fn (Policy $record) => $record->getRenewalPeriod()['end_date']),
                        Forms\Components\TextInput::make('premium_amount')
                            ->label('Prima')
                            ->numeric()
                            ->default(fn (Policy $record) => $record->premium_amount)
                            ->required(),
                        Forms\Components\Textarea::make('renewal_notes')
                            ->label('Notas de renovación')
                            ->rows(3),
                    ])
                    ->action(function (Policy $record, array $data): void {
                        // Create new policy as renewal
                        $newPolicy = $record->replicate([
                            'number',
                            'status',
                            'renewed_from_policy_id',
                            'renewed_to_policy_id',
                            'renewed_by',
                            'renewed_at',
                            'renewal_status',
                            'renewal_notes',
                        ]);

                        $newPolicy->fill([
                            'is_renewal' => true,
                            'renewed_from_policy_id' => $record->id,
                            'renewed_by' => auth()->id(),
                            'renewed_at' => now(),
                            'renewal_status' => RenewalStatus::COMPLETED,
                            'renewal_notes' => $data['renewal_notes'],
                            'start_date' => $data['start_date'],
                            'end_date' => $data['end_date'],
                            'premium_amount' => $data['premium_amount'],
                            'status' => PolicyStatus::PENDING,
                        ]);

                        $newPolicy->save();

                        // Update original policy
                        $record->update([
                            'renewed_to_policy_id' => $newPolicy->id,
                        ]);

                        Notification::make()
                            ->title('Póliza renovada exitosamente')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
            ])
            ->defaultSort('created_at', 'desc');
    }



    public static function getRelations(): array
    {
        return [
//            RelationManagers\IssuesRelationManager::class,
//            RelationManagers\DocumentsRelationManager::class,
        ];
    }

    public static function getActions(): array
    {
        return [
            Actions\Action::make('renew')
                ->label('Renovar Póliza')
                ->icon('heroicon-o-arrow-path')
                ->form([
                    Forms\Components\DatePicker::make('start_date')
                        ->label('Fecha de inicio')
                        ->required(),
                    Forms\Components\DatePicker::make('end_date')
                        ->label('Fecha de fin')
                        ->required(),
                    Forms\Components\Textarea::make('renewal_notes')
                        ->label('Notas de renovación')
                        ->required(),
                ])
                ->action(function (Policy $record, array $data): void {
                    $newPolicy = $record->replicate([
                        'start_date',
                        'end_date',
                        'renewal_status',
                        'renewed_at',
                        'is_renewal',
                    ]);

                    $newPolicy->start_date = $data['start_date'];
                    $newPolicy->end_date = $data['end_date'];
                    $newPolicy->is_renewal = true;
                    $newPolicy->renewal_status = RenewalStatus::COMPLETED;
                    $newPolicy->renewed_at = now();
                    $newPolicy->observations = ($record->observations ? $record->observations . "\n\n" : '')
                        . "=== Notas de Renovación ===\n" . $data['renewal_notes'];

                    $newPolicy->save();

                    // Update the original policy's renewal status
                    $record->renewal_status = RenewalStatus::COMPLETED;
                    $record->save();

                    Notification::make()
                        ->title('Póliza renovada exitosamente')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Renovar Póliza')
                ->modalDescription('Se creará una nueva póliza con los datos de la póliza actual. Por favor, especifique las nuevas fechas y notas de renovación.')
                ->modalSubmitActionLabel('Renovar')
                ->color('success'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPolicies::route('/'),
            'create' => Pages\CreatePolicy::route('/create'),
            'view' => Pages\ViewPolicy::route('/{record}'),
//            'view-contact' => Pages\ViewPolicyContact::route('/{record}/contact'),
            'edit' => Pages\EditPolicy::route('/{record}/edit'),
            'edit-contact' => Pages\EditPolicyContact::route('/{record}/edit/contact'),
            'edit-applicants' => Pages\EditPolicyApplicants::route('/{record}/edit/applicants'),
            'edit-income' => Pages\EditPolicyIncome::route('/{record}/edit/income'),
            'edit-life' => Pages\EditPolicyLife::route('/{record}/edit/life'),
            'documents' => Pages\ManagePolicyDocument::route('/{record}/documents'),
            'issues' => Pages\ManagePolicyIssues::route('/{record}/issues'),
            'payments' => Pages\EditPolicyPayments::route('/{record}/payments'),
//            'issues' => Pages\ManagePolicyDocument::route('/{record}/documents'),
        ];
    }
}
