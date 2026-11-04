<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    // use WithoutModelEvents;

    public function run(): void
    {
        $roles = ['super_admin', 'branch_manager', 'service_advisor', 'mechanic'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $branchPusat = Branch::firstOrCreate(
            ['code' => 'PDG-01'],
            [
                'name' => 'Cabang Utama Padang',
                'address' => 'Jl. Khatib Sulaiman, Padang',
                'phone' => '0751-123456',
                'is_active' => true,
            ]
        );

        Branch::firstOrCreate(
            ['code' => 'BKT-01'],
            [
                'name' => 'Cabang Bukittinggi',
                'address' => 'Jl. Sudirman, Bukittinggi',
                'phone' => '0752-654321',
                'is_active' => true,
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@erp.test'],
            [
                'name' => 'Super Admin Pusat',
                'password' => Hash::make('password'),
                'branch_id' => null,
                'is_active' => true,
            ]
        );
        $admin->assignRole('super_admin');

        $advisor = User::firstOrCreate(
            ['email' => 'advisor.pdg@erp.test'],
            [
                'name' => 'Andi (Service Advisor PDG)',
                'password' => Hash::make('password'),
                'branch_id' => $branchPusat->id,
                'is_active' => true,
            ]
        );
        $advisor->assignRole('service_advisor');
    }
}
