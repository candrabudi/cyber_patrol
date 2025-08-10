<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'superadmin@cyberpatrol.com'],
            [
                'username' => 'superadmin',
                'password' => Hash::make('supersecurepassword'),
                'role' => 'superadmin',
                'status' => true,
                'register_at' => now(),
                'ip_register_at' => '127.0.0.1',
                'remember_token' => Str::random(10),
            ]
        );

        $this->command->info('Superadmin CyberPatrol berhasil dibuat/diupdate.');
    }
}
