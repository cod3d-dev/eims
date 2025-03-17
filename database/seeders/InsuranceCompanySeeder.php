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
                'name' => 'Humana',
                'contact_name' => 'Humana Representative',
                'contact_email' => 'contact@humana.com',
                'contact_phone' => '555-0001',
                'portal_url' => 'https://www.humana.com',
                'is_active' => true,
            ],
            [
                'name' => 'Triple-S',
                'contact_name' => 'Triple-S Representative',
                'contact_email' => 'contact@ssspr.com',
                'contact_phone' => '555-0002',
                'portal_url' => 'https://www.ssspr.com',
                'is_active' => true,
            ],
            [
                'name' => 'MCS',
                'contact_name' => 'MCS Representative',
                'contact_email' => 'contact@mcs.com.pr',
                'contact_phone' => '555-0003',
                'portal_url' => 'https://www.mcs.com.pr',
                'is_active' => true,
            ],
            [
                'name' => 'First Medical',
                'contact_name' => 'First Medical Representative',
                'contact_email' => 'contact@firstmedical.com',
                'contact_phone' => '555-0004',
                'portal_url' => 'https://www.firstmedical.com',
                'is_active' => true,
            ],
            [
                'name' => 'Ambetter',
                'is_active' => true,
            ],
            [
                'name' => 'CareSource',
                'is_active' => true,
            ],
        ];

        foreach ($companies as $company) {
            InsuranceCompany::create($company);
        }
    }
}
