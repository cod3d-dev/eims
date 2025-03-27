<?php

namespace App\Filament\Resources\PolicyResource\Pages;

use App\Filament\Resources\PolicyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Contact;
use Illuminate\Support\Facades\Log;

class EditPolicy extends EditRecord
{
    protected static string $resource = PolicyResource::class;

    protected static ?string $navigationLabel = 'Poliza';
    protected static ?string $navigationIcon = 'iconoir-privacy-policy';

    public static string|\Filament\Support\Enums\Alignment $formActionsAlignment = 'end';


    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return false;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if(!isset($data['main_applicant'])) {
            $data['main_applicant'] = [];
        }

        if(!isset($data['contact_information'])) {
            $data['contact_information'] = [];
        }

        $data['contact_information']['first_name'] = $data->contact->first_name ?? null;
        $data['contact_information']['middle_name'] = $data->contact->middle_name ?? null;
        $data['contact_information']['last_name'] = $data->contact->last_name ?? null;
        $data['contact_information']['second_last_name'] = $data->contact->second_last_name ?? null;

//        dd($data);
        $data['main_applicant']['fullname'] = $data['contact_information']['first_name'] . ' ' . $data['contact_information']['middle_name'] . $data['contact_information']['last_name'] . $data['contact_information']['second_last_name'];

        if ($data['policy_us_state'] === 'KY' ) {
            $data['requires_aca'] = true;
        }
        return $data;
    }

//    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
//    {
//        try {
//            Log::info('Starting policy save...', [
//                'record_id' => $this->record->id,
//                'form_data' => $this->data,
//            ]);
//
//            parent::save($shouldRedirect);
//
//            Log::info('Policy saved successfully', [
//                'record_id' => $this->record->id,
//            ]);
//        } catch (\Exception $e) {
//            Log::error('Error saving policy', [
//                'record_id' => $this->record->id,
//                'error' => $e->getMessage(),
//                'trace' => $e->getTraceAsString(),
//            ]);
//
//            throw $e;
//        }
//    }
//
//    protected function mutateFormDataBeforeSave(array $data): array
//    {
//        Log::info('Mutating form data before save', [
//            'original_data' => $data,
//        ]);
//
//        if (isset($data['contact_id']) && isset($data['contact_information'])) {
//            $contact = Contact::find($data['contact_id']);
//            if ($contact) {
//                $contact->update([
//                    'first_name' => $data['contact_information']['first_name'],
//                    'last_name' => $data['contact_information']['last_name'],
//                    'middle_name' => $data['contact_information']['middle_name'],
//                    'second_last_name' => $data['contact_information']['second_last_name'],
//                    'date_of_birth' => $data['contact_information']['date_of_birth'],
//                    'gender' => $data['contact_information']['gender'],
//                    'marital_status' => $data['contact_information']['marital_status'],
//                    'phone' => $data['contact_information']['phone'],
//                    'phone2' => $data['contact_information']['phone2'],
//                    'whatsapp' => $data['contact_information']['whatsapp'],
//                    'email_address' => $data['contact_information']['email_address'],
//                    'is_tobacco_user' => $data['contact_information']['is_tobacco_user'],
//                    'is_pregnant' => $data['contact_information']['is_pregnant'],
//                    'is_eligible_for_coverage' => $data['contact_information']['is_eligible_for_coverage'],
//                    'weight' => $data['contact_information']['weight'] ?? null,
//                    'height' => $data['contact_information']['height'] ?? null,
//                    'address_line_1' => $data['contact_information']['address_line_1'] ?? null,
//                    'address_line_2' => $data['contact_information']['address_line_2'] ?? null,
//                    'city' => $data['contact_information']['city'] ?? null,
//                    'state_province' => $data['contact_information']['state_province'] ?? null,
//                    'zip_code' => $data['contact_information']['zip_code'] ?? null,
//                    'county' => $data['contact_information']['county'] ?? null,
//                    'employer_name_1' => $data['contact_information']['employer_name_1'] ?? null,
//                    'employer_phone_1' => $data['contact_information']['employer_phone_1'] ?? null,
//                    'position_1' => $data['contact_information']['position_1'] ?? null,
//                    'annual_income_1' => $data['contact_information']['annual_income_1'] ?? null,
//                    'employer_name_2' => $data['contact_information']['employer_name_2'] ?? null,
//                    'employer_phone_2' => $data['contact_information']['employer_phone_2'] ?? null,
//                    'position_2' => $data['contact_information']['position_2'] ?? null,
//                    'annual_income_2' => $data['contact_information']['annual_income_2'] ?? null,
//                    'immigration_status' => $data['contact_information']['immigration_status'] ?? null,
//                    'immigration_status_category' => $data['contact_information']['immigration_status_category'] ?? null,
//                    'passport_number' => $data['contact_information']['passport_number'] ?? null,
//                    'uscis_number' => $data['contact_information']['uscis_number'] ?? null,
//                    'ssn' => $data['contact_information']['ssn'] ?? null,
//                ]);
//            }
//        }
//
//        Log::info('Mutated form data', [
//            'mutated_data' => $data,
//        ]);
//
//        return $data;
//    }
}
