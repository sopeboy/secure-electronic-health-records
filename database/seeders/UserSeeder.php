<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Demo accounts for local testing only. Password for all: password
 * Change or remove these before any public deployment.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['System Admin',     'admin@sehrs.test',   Role::ADMIN],
            ['Dr. Amina Bello',  'doctor@sehrs.test',  Role::DOCTOR],
            ['Dr. Musa Garba',   'doctor2@sehrs.test', Role::DOCTOR],
            ['Nurse Hauwa Sani', 'nurse@sehrs.test',   Role::NURSE],
            ['Lab Tech Yusuf',   'lab@sehrs.test',     Role::LAB],
        ];

        foreach ($accounts as [$name, $email, $roleName]) {
            $user = User::firstOrNew(['email' => $email]);
            $user->name = $name;
            $user->password = 'password';          // hashed automatically by the cast
            $user->role_id = Role::where('name', $roleName)->value('id');
            $user->status = User::STATUS_ACTIVE;
            $user->email_verified_at = now();
            $user->save();
        }
    }
}
