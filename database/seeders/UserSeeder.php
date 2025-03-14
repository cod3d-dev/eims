<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Ghercy Segovia',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'Ricardo Segovia',
            'email' => 'agent1@example.com',
            'password' => Hash::make('password'),
            'role' => 'agent'
        ]);

        User::create([
            'name' => 'Fhiona Segovia',
            'email' => 'agent2@example.com',
            'password' => Hash::make('password'),
            'role' => 'supervisor'
        ]);

        User::create([
            'name' => 'Raul Medrano',
            'email' => 'agent3@example.com',
            'password' => Hash::make('password'),
            'role' => 'agent'
        ]);

        User::create([
            'name' => 'Omar Ostos',
            'email' => 'agent4@example.com',
            'password' => Hash::make('password'),
            'role' => 'agent'
        ]);

        User::create([
            'name' => 'Carlos Rojas',
            'email' => 'agent5@example.com',
            'password' => Hash::make('password'),
            'role' => 'agent'
        ]);

        User::create([
            'name' => 'Christell',
            'email' => 'agent6@example.com',
            'password' => Hash::make('password'),
            'role' => 'agent'
        ]);
    }
}
