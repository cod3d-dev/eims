<?php

namespace Database\Factories;

use App\Enums\DocumentStatus;
use App\Enums\PolicyStatus;
use App\Enums\RenewalStatus;
use App\Enums\UsState;
use App\Models\Agent;
use App\Models\Contact;
use App\Models\InsuranceCompany;
use App\Models\PolicyType;
use App\Models\Quote;
use App\Models\User;
use App\ValueObjects\Applicant;
use App\ValueObjects\ApplicantCollection;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Policy>
 */


class PolicyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     public $faker;

    /**
     * Create a new factory instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->faker = \Faker\Factory::create('es_VE');
    }


    public function definition(): array
    {
        $contact = Contact::inRandomOrder()->first() ?? Contact::factory()->create();
        $user = User::inRandomOrder()->first();
        $policyType = PolicyType::inRandomOrder()->first();
        $insuranceCompany = InsuranceCompany::inRandomOrder()->first();
        $agent = Agent::inRandomOrder()->first();
        $quote = Quote::inRandomOrder()->first();

        // Generate 0-3 additional applicants
        $additionalApplicantsCount = $this->faker->numberBetween(0, 3);
        $totalFamilyMembers = $additionalApplicantsCount + 1; // Main applicant + additional applicants

        // Create main applicant based on contact data
        $mainApplicant = $this->createMainApplicantFromContact($contact);

        // Create additional applicants
        $additionalApplicants = $this->createAdditionalApplicants($additionalApplicantsCount);

        // Generate random dates
        $effectiveDate = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $expirationDate = (clone $effectiveDate)->modify('+1 year');

        // Generate contact information
        $contactInformation = $this->generateContactInformation($contact);

        // Generate prescription drugs
        $prescriptionDrugs = $this->generatePrescriptionDrugs();

        return [
            // Basic Information
            'contact_id' => $contact->id,
            'user_id' => $user->id,
            'insurance_company_id' => $insuranceCompany->id,
            'policy_type_id' => $policyType->id,
            'quote_id' => $quote?->id,
            'agent_id' => $agent->id,
            'policy_number' => $this->faker->regexify('[A-Z]{2}[0-9]{6}'),
            'policy_year' => now()->format('Y'),
            'policy_us_state' => $this->faker->randomElement(UsState::class),
            'kynect_case_number' => $this->faker->optional()->regexify('[0-9]{8}'),
            'insurance_company_policy_number' => $this->faker->optional()->regexify('[A-Z]{3}[0-9]{7}'),
            'policy_plan' => $this->faker->randomElement(['Bronze', 'Silver', 'Gold', 'Platinum']),
            'policy_level' => $this->faker->randomElement(['Basic', 'Standard', 'Premium']),

            // Financial Information
            'policy_total_cost' => $totalCost = $this->faker->randomFloat(2, 5000, 20000),
            'policy_total_subsidy' => $subsidy = $this->faker->optional(0.7)->randomFloat(2, 1000, $totalCost * 0.8),
            'premium_amount' => $subsidy ? $totalCost - $subsidy : $totalCost,
            'coverage_amount' => $this->faker->randomFloat(2, 100000, 1000000),
            'recurring_payment' => $this->faker->boolean(80),

            // Dates
            'effective_date' => $effectiveDate,
            'expiration_date' => $expirationDate,
            'first_payment_date' => $this->faker->dateTimeBetween('-30 days', '+30 days'),
            'last_payment_date' => $this->faker->optional(0.5)->dateTimeBetween('-30 days', 'now'),
            'preferred_payment_day' => $this->faker->numberBetween(1, 28),

            // Payment Status
            'initial_paid' => $this->faker->boolean(70),
            'autopay' => $this->faker->boolean(60),
            'aca' => $this->faker->boolean(50),
            'document_status' => $this->faker->randomElement(DocumentStatus::class),
            'observations' => $this->faker->optional(0.7)->paragraph(),
            'client_notified' => $this->faker->boolean(80),

            // Family Information
            'main_applicant' => $mainApplicant,
            'additional_applicants' => $additionalApplicants,
            'total_family_members' => $totalFamilyMembers,
            'total_applicants' => $totalFamilyMembers,

            // Additional Information
            'estimated_household_income' => $this->faker->randomFloat(2, 30000, 150000),
            'preferred_doctor' => $this->faker->optional(0.5)->name(),
            'prescription_drugs' => $prescriptionDrugs,
            'contact_information' => $contactInformation,

            // Policy Status and Dates
            'start_date' => $effectiveDate,
            'end_date' => $expirationDate,
            'status' => $this->faker->randomElement(PolicyStatus::class),
            'status_changed_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'status_changed_by' => $user->id,
            'notes' => $this->faker->optional(0.7)->paragraph(),

            // Payment Information (encrypted fields)
            'payment_card_type' => $this->faker->optional(0.6)->randomElement(['visa', 'mastercard', 'amex', 'discover']),
            'payment_card_bank' => $this->faker->optional(0.6)->company(),
            'payment_card_holder' => $this->faker->optional(0.6)->name(),
            'payment_card_number' => $this->faker->optional(0.6)->creditCardNumber(),
            'payment_card_exp_month' => $this->faker->optional(0.6)->numberBetween(1, 12),
            'payment_card_exp_year' => $this->faker->optional(0.6)->numberBetween(now()->format('Y'), now()->addYears(5)->format('Y')),
            'payment_card_cvv' => $this->faker->optional(0.6)->numberBetween(100, 999),

            // Bank Account Information (encrypted fields)
            'payment_bank_account_bank' => $this->faker->optional(0.4)->company(),
            'payment_bank_account_holder' => $this->faker->optional(0.4)->name(),
            'payment_bank_account_aba' => $this->faker->optional(0.4)->regexify('[0-9]{9}'),
            'payment_bank_account_number' => $this->faker->optional(0.4)->regexify('[0-9]{10,12}'),

            // Billing Address (encrypted fields)
            'billing_address_1' => $this->faker->optional(0.7)->streetAddress(),
            'billing_address_2' => $this->faker->optional(0.3)->secondaryAddress(),
            'billing_address_city' => $this->faker->optional(0.7)->city(),
            'billing_address_state' => $this->faker->optional(0.7)->state(),
            'billing_address_zip' => $this->faker->optional(0.7)->postcode(),

            // Renewal fields
            'is_renewal' => $isRenewal = $this->faker->boolean(20),
            'renewed_from_policy_id' => $isRenewal ? null : null, // Would need to set this manually
            'renewed_to_policy_id' => null, // Would need to set this manually
            'renewed_by' => $isRenewal ? $user->id : null,
            'renewed_at' => $isRenewal ? $this->faker->dateTimeBetween('-30 days', 'now') : null,
            'renewal_status' => $isRenewal ? $this->faker->randomElement(RenewalStatus::cases()) : null,
            'renewal_notes' => $isRenewal ? $this->faker->optional(0.7)->paragraph() : null,

            // Audit Information
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ];
    }

    /**
     * Create a main applicant from contact data
     */
    private function createMainApplicantFromContact(Contact $contact): array
    {
        return [
            'gender' => $contact->gender ?? $this->faker->randomElement(['male', 'female']),
            'date_of_birth' => $contact->date_of_birth ?? $this->faker->date('Y-m-d', '-60 years'),
            'relationship' => 'self',
            'first_name' => $contact->first_name,
            'middle_name' => $contact->middle_name,
            'last_name' => $contact->last_name,
            'second_last_name' => $contact->second_last_name,
            'fullname' => trim($contact->first_name . ' ' . $contact->last_name),
            'is_tobacco_user' => $contact->is_tobacco_user ?? $this->faker->boolean(20),
            'is_pregnant' => $contact->is_pregnant ?? $this->faker->boolean(10),
            'is_eligible_for_coverage' => $contact->is_eligible_for_coverage ?? $this->faker->boolean(90),
            'country_of_birth' => $contact->country_of_birth,
            'civil_status' => $contact->marital_status,
            'phone1' => $contact->phone,
            'email_address' => $contact->email_address,
            'height' => $contact->height,
            'weight' => $contact->weight,
            'preferred_doctor' => null,
            'prescription_drugs' => [],
            'member_ssn' => $contact->ssn,
            'member_ssn_date' => $contact->ssn_issue_date,
            'member_passport' => $contact->passport_number,
            'member_green_card' => $contact->green_card_number,
            'member_green_card_expedition_date' => null,
            'member_green_card_expiration_date' => $contact->green_card_expiration_date,
            'member_work_permit' => $contact->work_permit_number,
            'member_work_permit_expedition_date' => null,
            'member_work_permit_expiration_date' => $contact->work_permit_expiration_date,
            'member_driver_license' => $contact->driver_license_number,
            'member_driver_license_expedition_date' => null,
            'member_driver_license_expiration_date' => $contact->driver_license_expiration_date,
            'member_uscis' => $contact->uscis_number,
            'member_inmigration_status' => $contact->immigration_status,
            'member_inmigration_status_category' => $contact->immigration_status_category,
            'employer_1_name' => $contact->employer_name_1,
            'employer_1_role' => $contact->position_1,
            'employer_1_phone' => $contact->employer_phone_1,
            'employer_1_income' => $contact->annual_income_1,
            'employer_2_name' => $contact->employer_name_2,
            'employer_2_role' => $contact->position_2,
            'employer_2_phone' => $contact->employer_phone_2,
            'employer_2_income' => $contact->annual_income_2,
            'employer_3_name' => $contact->employer_name_3,
            'employer_3_role' => $contact->position_3,
            'employer_3_phone' => $contact->employer_phone_3,
            'employer_3_income' => $contact->annual_income_3,
            'yearly_income' => $this->calculateTotalIncome($contact),
            'is_self_employed' => $this->faker->boolean(30),
            'self_employed_profession' => $this->faker->optional()->jobTitle(),
            'income_per_hour' => $this->faker->optional()->randomFloat(2, 15, 100),
            'hours_per_week' => $this->faker->optional()->numberBetween(10, 40),
            'income_per_extra_hour' => $this->faker->optional()->randomFloat(2, 20, 150),
            'extra_hours_per_week' => $this->faker->optional()->numberBetween(0, 20),
            'weeks_per_year' => $this->faker->optional()->numberBetween(40, 52),
            'self_employed_yearly_income' => $this->faker->optional()->randomFloat(2, 20000, 150000),
            'age' => Carbon::parse($contact->date_of_birth)->age ?? $this->faker->numberBetween(18, 80),
        ];
    }

    /**
     * Create an array of additional applicants
     */
    private function createAdditionalApplicants(int $count): array
    {
        $applicants = [];

        $relationships = ['spouse', 'child', 'parent', 'sibling'];

        for ($i = 0; $i < $count; $i++) {
            $gender = $this->faker->randomElement(['male', 'female']);
            $firstName = $gender === 'male' ? $this->faker->firstNameMale() : $this->faker->firstNameFemale();
            $lastName = $this->faker->lastName();
            $relationship = $this->faker->randomElement($relationships);

            // Adjust age based on relationship
            $age = match($relationship) {
                'spouse' => $this->faker->numberBetween(18, 80),
                'child' => $this->faker->numberBetween(0, 26),
                'parent' => $this->faker->numberBetween(45, 90),
                'sibling' => $this->faker->numberBetween(18, 70),
                default => $this->faker->numberBetween(18, 80),
            };

            $dob = Carbon::now()->subYears($age)->subDays($this->faker->numberBetween(0, 365))->format('Y-m-d');

            $applicants[] = [
                'gender' => $gender,
                'date_of_birth' => $dob,
                'relationship' => $relationship,
                'first_name' => $firstName,
                'middle_name' => $this->faker->optional(0.3)->firstName(),
                'last_name' => $lastName,
                'second_last_name' => $this->faker->optional(0.3)->lastName(),
                'fullname' => trim($firstName . ' ' . $lastName),
                'is_tobacco_user' => $this->faker->boolean(20),
                'is_pregnant' => $gender === 'female' ? $this->faker->boolean(10) : false,
                'is_eligible_for_coverage' => $this->faker->boolean(90),
                'country_of_birth' => $this->faker->country(),
                'civil_status' => $relationship === 'child' ? 'single' : $this->faker->randomElement(['single', 'married', 'divorced', 'widowed']),
                'phone1' => $relationship === 'child' && $age < 18 ? null : $this->faker->phoneNumber(),
                'email_address' => $relationship === 'child' && $age < 18 ? null : $this->faker->optional(0.7)->safeEmail(),
                'height' => $this->faker->randomFloat(2, 3, 7),
                'weight' => $this->faker->randomFloat(2, 30, 300),
                'preferred_doctor' => $this->faker->optional(0.3)->name(),
                'prescription_drugs' => $this->faker->boolean(30) ? $this->generatePrescriptionDrugs(1, 2) : [],
                'member_ssn' => $this->faker->optional(0.7)->regexify('[0-9]{3}-[0-9]{2}-[0-9]{4}'),
                'member_ssn_date' => $this->faker->optional(0.5)->date(),
                'member_passport' => $this->faker->optional(0.3)->regexify('[A-Z][0-9]{8}'),
                'member_green_card' => $this->faker->optional(0.3)->regexify('[A-Z][0-9]{8}'),
                'member_green_card_expedition_date' => $this->faker->optional(0.3)->date(),
                'member_green_card_expiration_date' => $this->faker->optional(0.3)->date(),
                'member_work_permit' => $this->faker->optional(0.3)->regexify('[A-Z][0-9]{8}'),
                'member_work_permit_expedition_date' => $this->faker->optional(0.3)->date(),
                'member_work_permit_expiration_date' => $this->faker->optional(0.3)->date(),
                'member_driver_license' => $age >= 16 ? $this->faker->optional(0.5)->regexify('[A-Z][0-9]{7}') : null,
                'member_driver_license_expedition_date' => $age >= 16 ? $this->faker->optional(0.5)->date() : null,
                'member_driver_license_expiration_date' => $age >= 16 ? $this->faker->optional(0.5)->date() : null,
                'member_uscis' => $this->faker->optional(0.3)->regexify('[0-9]{9}'),
                'member_inmigration_status' => $this->faker->optional(0.5)->randomElement(['citizen', 'permanent_resident', 'temporary_resident', 'visa_holder']),
                'member_inmigration_status_category' => $this->faker->optional(0.5)->randomElement(['A', 'B', 'C', 'D']),
                'age' => $age,
            ];
        }

        return $applicants;
    }

    /**
     * Generate contact information array
     */
    private function generateContactInformation(Contact $contact): array
    {
        return [
            'name' => trim($contact->first_name . ' ' . $contact->last_name),
            'email' => $contact->email_address,
            'phone' => $contact->phone,
            'phone2' => $contact->phone2,
            'whatsapp' => $contact->whatsapp,
            'address' => [
                'line1' => $contact->address_line_1,
                'line2' => $contact->address_line_2,
                'city' => $contact->city,
                'state' => $contact->state_province,
                'zip' => $contact->zip_code,
                'county' => $contact->county,
            ],
            'mailing_address' => $contact->is_same_as_physical ? null : [
                'line1' => $contact->mailing_address_line_1,
                'line2' => $contact->mailing_address_line_2,
                'city' => $contact->mailing_city,
                'state' => $contact->mailing_state_province,
                'zip' => $contact->mailing_zip_code,
            ],
            'preferred_language' => $contact->preferred_language ?? 'spanish',
            'preferred_contact_method' => $contact->preferred_contact_method,
            'preferred_contact_time' => $contact->preferred_contact_time,
        ];
    }

    /**
     * Generate prescription drugs array
     */
    private function generatePrescriptionDrugs(int $min = 0, int $max = 5): array
    {
        $drugs = [];
        $count = $this->faker->numberBetween($min, $max);

        if ($count <= 0) {
            return $drugs;
        }

        $commonDrugs = [
            'Lisinopril', 'Atorvastatin', 'Levothyroxine', 'Metformin',
            'Amlodipine', 'Metoprolol', 'Omeprazole', 'Simvastatin',
            'Losartan', 'Albuterol', 'Gabapentin', 'Hydrochlorothiazide',
            'Sertraline', 'Acetaminophen', 'Ibuprofen', 'Aspirin',
            'Amoxicillin', 'Azithromycin', 'Fluoxetine', 'Prednisone'
        ];

        $drugIndices = array_rand($commonDrugs, min($count, count($commonDrugs)));

        if (!is_array($drugIndices)) {
            $drugIndices = [$drugIndices];
        }

        foreach ($drugIndices as $index) {
            $drugs[] = [
                'name' => $commonDrugs[$index],
                'dosage' => $this->faker->randomElement(['5mg', '10mg', '20mg', '25mg', '50mg', '100mg']),
                'frequency' => $this->faker->randomElement(['daily', 'twice daily', 'as needed', 'weekly']),
            ];
        }

        return $drugs;
    }

    /**
     * Calculate total income from all sources
     */
    private function calculateTotalIncome(Contact $contact): float
    {
        $total = 0;

        if ($contact->annual_income_1) {
            $total += $contact->annual_income_1;
        }

        if ($contact->annual_income_2) {
            $total += $contact->annual_income_2;
        }

        if ($contact->annual_income_3) {
            $total += $contact->annual_income_3;
        }

        // If no income is set, generate a random one
        if ($total === 0) {
            $total = $this->faker->randomFloat(2, 20000, 120000);
        }

        return $total;
    }
}
