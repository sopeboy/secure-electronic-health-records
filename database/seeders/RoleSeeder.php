<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [Role::ADMIN,  'Administrator', 'Manages staff accounts and roles; views audit logs'],
            [Role::DOCTOR, 'Doctor',        'Diagnoses and prescribes for assigned patients'],
            [Role::NURSE,  'Nurse',         'Views assigned patients and records vital signs'],
            [Role::LAB,    'Lab Staff',     'Records laboratory results'],
        ];

        foreach ($roles as [$name, $label, $description]) {
            Role::updateOrCreate(['name' => $name], compact('label', 'description'));
        }
    }
}
