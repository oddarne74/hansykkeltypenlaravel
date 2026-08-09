<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = (string) config('admin.email');

        if (User::where('email', $email)->exists()) {
            return;
        }

        $configured = config('admin.password');
        $password = is_string($configured) && $configured !== '' ? $configured : Str::password(16);

        User::create([
            'name' => (string) config('admin.name'),
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        if ($password !== $configured) {
            $this->command->warn("Admin-bruker opprettet: {$email} med passord: {$password} — bytt passord etter første innlogging.");
        }
    }
}
