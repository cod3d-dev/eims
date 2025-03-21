<?php

namespace Database\Factories;

use App\Enums\DocumentStatus;
use App\Enums\QuoteStatus;
use App\Models\Agent;
use App\Models\Contact;
use App\Models\InsuranceCompany;
use App\Models\PolicyType;
use App\Models\User;
use App\ValueObjects\Applicant;
use App\ValueObjects\ApplicantCollection;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quote>
 */
class QuoteFactory extends Factory
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

        // Generate 0-3 additional applicants
        $additionalApplicantsCount = $this->faker->numberBetween(0, 3);
        $totalFamilyMembers = $additionalApplicantsCount + 1; // Main applicant + additional applicants

        // Create main applicant based on contact data
        $mainApplicant = $this->createMainApplicantFromContact($contact);

        // Create additional applicants
        $additionalApplicants = $this->createAdditionalApplicants($additionalApplicantsCount);

        // Generate random dates
        $startDate = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $endDate = (clone $startDate)->modify('+1 year');
        $validUntil = (clone $startDate)->modify('+30 days');

        // Generate contact information
        $contactInformation = $this->generateContactInformation($contact);

        return [
            'contact_id' => $contact->id,
            'contact_information' => $contactInformation,
            'user_id' => $user->id,
            'policy_id' => null,
            'insurance_company_id' => $insuranceCompany?->id,
            'agent_id' => $agent?->id,
            'policy_type_id' => $policyType?->id,
            'premium_amount' => $this->faker->randomFloat(2, 50, 1000),
            'coverage_amount' => $this->faker->randomFloat(2, 10000, 1000000),
            'year' => Carbon::now()->year,

            // Applicants Information
            'main_applicant' => $mainApplicant,
            'additional_applicants' => $additionalApplicants,
            'total_family_members' => $totalFamilyMembers,
            'total_applicants' => $totalFamilyMembers,

            // Additional Information
            'estimated_household_income' => $this->faker->randomFloat(2, 20000, 150000),
            'preferred_doctor' => $this->faker->optional()->name(),
            'prescription_drugs' => $this->generatePrescriptionDrugs(),

            // Quote Status and Dates
            'start_date' => $startDate,
            'end_date' => $endDate,
            'valid_until' => $validUntil,
            'status' => $this->faker->randomElement(QuoteStatus::cases()),
            'notes' => $this->faker->optional()->paragraph(),
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
                'member_uscis' => $this->faker->optional(0.3)->regexify('[0-9]{9}'),
                'member_inmigration_status' => $this->faker->optional(0.5)->randomElement(['citizen', 'permanent_resident', 'temporary_resident', 'visa_holder']),
                'member_inmigration_status_category' => $this->faker->optional(0.5)->randomElement(['employment', 'family', 'student', 'refugee']),
                'employer_1_name' => $age >= 18 ? $this->faker->optional(0.7)->company() : null,
                'employer_1_role' => $age >= 18 ? $this->faker->optional(0.7)->jobTitle() : null,
                'employer_1_phone' => $age >= 18 ? $this->faker->optional(0.7)->phoneNumber() : null,
                'employer_1_income' => $age >= 18 ? $this->faker->optional(0.7)->randomFloat(2, 20000, 150000) : null,
                'employer_2_name' => $age >= 18 ? $this->faker->optional(0.3)->company() : null,
                'employer_2_role' => $age >= 18 ? $this->faker->optional(0.3)->jobTitle() : null,
                'employer_2_phone' => $age >= 18 ? $this->faker->optional(0.3)->phoneNumber() : null,
                'employer_2_income' => $age >= 18 ? $this->faker->optional(0.3)->randomFloat(2, 20000, 150000) : null,
                'employer_3_name' => $age >= 18 ? $this->faker->optional(0.1)->company() : null,
                'employer_3_role' => $age >= 18 ? $this->faker->optional(0.1)->jobTitle() : null,
                'employer_3_phone' => $age >= 18 ? $this->faker->optional(0.1)->phoneNumber() : null,
                'employer_3_income' => $age >= 18 ? $this->faker->optional(0.1)->randomFloat(2, 20000, 150000) : null,
                'yearly_income' => $age >= 18 ? $this->faker->optional(0.7)->randomFloat(2, 20000, 150000) : null,
                'is_self_employed' => $age >= 18 ? $this->faker->boolean(30) : false,
                'self_employed_profession' => $age >= 18 && $this->faker->boolean(30) ? $this->faker->jobTitle() : null,
                'income_per_hour' => $age >= 18 ? $this->faker->optional(0.5)->randomFloat(2, 15, 100) : null,
                'hours_per_week' => $age >= 18 ? $this->faker->optional(0.5)->numberBetween(10, 40) : null,
                'income_per_extra_hour' => $age >= 18 ? $this->faker->optional(0.3)->randomFloat(2, 20, 150) : null,
                'extra_hours_per_week' => $age >= 18 ? $this->faker->optional(0.3)->numberBetween(0, 20) : null,
                'weeks_per_year' => $age >= 18 ? $this->faker->optional(0.5)->numberBetween(40, 52) : null,
                'self_employed_yearly_income' => $age >= 18 ? $this->faker->optional(0.3)->randomFloat(2, 20000, 150000) : null,
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

        $commonDrugs = [
            'Lisinopril', 'Atorvastatin', 'Levothyroxine', 'Metformin',
            'Amlodipine', 'Metoprolol', 'Omeprazole', 'Simvastatin',
            'Losartan', 'Albuterol', 'Gabapentin', 'Hydrochlorothiazide',
            'Sertraline', 'Acetaminophen', 'Ibuprofen', 'Aspirin',
            'Fluoxetine', 'Amoxicillin', 'Prednisone', 'Furosemide'
        ];

        for ($i = 0; $i < $count; $i++) {
            $drugs[] = [
                'name' => $this->faker->randomElement($commonDrugs),
                'dosage' => $this->faker->randomElement(['5mg', '10mg', '20mg', '25mg', '50mg', '100mg']),
                'frequency' => $this->faker->randomElement(['daily', 'twice daily', 'as needed', 'weekly']),
                'reason' => $this->faker->optional()->sentence(3),
            ];
        }

        return $drugs;
    }

    /**
     * Calculate total income from all sources
     */
    private function calculateTotalIncome(Contact $contact): ?float
    {
        $totalIncome = 0;
        $hasIncome = false;

        if ($contact->annual_income_1) {
            $totalIncome += $contact->annual_income_1;
            $hasIncome = true;
        }

        if ($contact->annual_income_2) {
            $totalIncome += $contact->annual_income_2;
            $hasIncome = true;
        }

        if ($contact->annual_income_3) {
            $totalIncome += $contact->annual_income_3;
            $hasIncome = true;
        }

        return $hasIncome ? $totalIncome : null;
    }
}
