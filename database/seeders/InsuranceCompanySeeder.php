<?php

namespace Database\Seeders;

use App\Models\InsuranceCompany;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InsuranceCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'name' => 'Molina',
                'code' => 'MOL',
                'is_active' => true,
            ],
            [
                'name' => 'Ambetter Health',
                'code' => 'AMB',
                'is_active' => true,
            ],
            [
                'name' => 'CareSource',
                'code' => 'CAR',
                'is_active' => true,
            ],
            [
                'name' => 'Oscar',
                'code' => 'OSC',
                'is_active' => true,
            ],
            [
                'name' => 'Anthem',
                'code' => 'ANT',
                'is_active' => true,
            ],
            [
                'name' => 'Cigna Healthcare',
                'code' => 'CIG',
                'is_active' => true,
            ],
            [
                'name' => 'UnitedHealthcare',
                'code' => 'UHC',
                'is_active' => true,
            ],
            [
                'name' => 'Blue Cross Blue Shield',
                'code' => 'BSC',
                'is_active' => true,
            ],
            [
                'name' => 'Sentara',
                'code' => 'SEN',
                'is_active' => true,
            ],
            [
                'name' => 'Aetna',
                'code' => 'AET',
                'is_active' => true,
            ],
            [
                'name' => 'Kaiser Permanente',
                'code' => 'KAI',
                'is_active' => true,
            ],
        ];

        foreach ($companies as $company) {
            InsuranceCompany::create($company);
        }
    }
}
