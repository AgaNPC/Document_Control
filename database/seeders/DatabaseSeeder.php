<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Document;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Users for 3-layer workflow & RBAC
        $users = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@company.com',
                'password' => Hash::make('password'),
                'role' => 'Karyawan',
                'department' => 'K3LH',
            ],
            [
                'name' => 'Ahmad PIC (L1)',
                'email' => 'ahmad.pic@company.com',
                'password' => Hash::make('password'),
                'role' => 'PIC',
                'department' => 'K3LH',
            ],
            [
                'name' => 'Dedi Section Head (L2)',
                'email' => 'dedi.sechead@company.com',
                'password' => Hash::make('password'),
                'role' => 'Section Head',
                'department' => 'K3LH',
            ],
            [
                'name' => 'Eko Department Head (L3)',
                'email' => 'eko.depthead@company.com',
                'password' => Hash::make('password'),
                'role' => 'Department Head',
                'department' => 'K3LH',
            ],
            [
                'name' => 'Rian IT Administrator',
                'email' => 'rian.it@company.com',
                'password' => Hash::make('password'),
                'role' => 'IT Admin',
                'department' => 'IT System',
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(['email' => $u['email']], $u);
        }

        // 2. Run Mass Document Seeder (2,500+ documents)
        $this->call(MassDocumentSeeder::class);
    }
}
