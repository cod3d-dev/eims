<?php

namespace Database\Seeders;

use App\Models\KynectFPL;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            UserSeeder::class,
            ContactSeeder::class,
            InsuranceCompanySeeder::class,
            AgentSeeder::class,
            PolicyTypeSeeder::class,

            // QuoteSeeder::class,
            // PolicySeeder::class,
            // IssueTypeSeeder::class,
            // DocumentTypeSeeder::class,

        ]);

        $kynectFPLData = [
            'year' => 2024,
            'members_1' => 1918,
            'members_2' => 2591,
            'members_3' => 3265,
            'members_4' => 3939,
            'members_5' => 4613,
            'members_6' => 5286,
            'members_7' => 5960,
            'members_8' => 6634,
            'additional_member' => 674
        ];

        KynectFPL::create($kynectFPLData);
    }
}
