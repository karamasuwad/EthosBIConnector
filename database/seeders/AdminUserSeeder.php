<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'System Administrator',
            'email' => 'karama@ethostechhub.com',
            'telephone' => '+254723512374',
            'password' => Hash::make('Ethos@TH'),
            'is_active' => true,
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: karama@ethostechhub.com');
        $this->command->info('Password: Ethos@TH');
    }
}
