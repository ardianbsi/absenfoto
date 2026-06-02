<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roleSuperAdmin = Role::where('name', 'super_admin')->first()->id;
        $roleHr = Role::where('name', 'hr')->first()->id;
        $roleManager = Role::where('name', 'manager')->first()->id;
        $roleEmployee = Role::where('name', 'employee')->first()->id;

        User::create([
            'name' => 'Super Admin',
            'username' => 'super.admin',
            'email' => 'superadmin@app.com',
            'password' => bcrypt('password'),
            'is_active' => true,
            'role_id' => $roleSuperAdmin,
            'theme_preference' => 'light',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        User::create([
            'name' => 'Dewi Rahayu',
            'username' => 'dewi.rahayu',
            'email' => 'hr1@app.com',
            'password' => bcrypt('password'),
            'is_active' => true,
            'role_id' => $roleHr,
            'theme_preference' => 'light',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        User::create([
            'name' => 'Fitri Handayani',
            'username' => 'fitri.handayani',
            'email' => 'hr2@app.com',
            'password' => bcrypt('password'),
            'is_active' => true,
            'role_id' => $roleHr,
            'theme_preference' => 'light',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        $managerNames = [
            'Bambang Wijaya', 'Ratna Lestari', 'Eko Nugroho', 'Sari Utami', 'Hendra Kusuma',
        ];

        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => $managerNames[$i - 1],
                'username' => 'manager' . $i,
                'email' => 'manager' . $i . '@app.com',
                'password' => bcrypt('password'),
                'is_active' => true,
                'role_id' => $roleManager,
                'theme_preference' => 'light',
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
        }

        $employeeNames = [
            'Agus Pratama', 'Ahmad Hidayat', 'Budi Santoso', 'Cici Wulandari',
            'Deni Saputra', 'Dian Pertiwi', 'Fajar Nugroho', 'Gilang Hermawan',
            'Gunawan Wibowo', 'Hendra Setiawan', 'Herman Maulana', 'Hesti Purwanti',
            'Indah Kusuma', 'Indra Nasution', 'Intan Siregar', 'Joko Purnomo',
            'Kadek Sinaga', 'Kurniawan Ginting', 'Linda Sembiring', 'Made Tamba',
            'Mega Manurung', 'Nina Siahaan', 'Nurul Simanjuntak', 'Oka Sitompul',
            'Olivia Pasaribu', 'Pramono Situmorang', 'Putri Nababan', 'Ratna Siregar',
            'Rina Simatupang', 'Rudi Sirait', 'Sari Pangaribuan', 'Slamet Napitupulu',
            'Tri Lumbantoruan', 'Utami Panggabean', 'Wahyu Silalahi', 'Widi Situmeang',
            'Yanti Marbun', 'Zainal Purba', 'Bagus Siahaan', 'Dewi Simbolon',
        ];

        for ($i = 1; $i <= 50; $i++) {
            $nameIndex = ($i - 1) % count($employeeNames);
            $name = $employeeNames[$nameIndex];
            if ($i > count($employeeNames)) {
                $name = $name . ' ' . ($i);
            }

            User::create([
                'name' => $name,
                'username' => 'employee' . $i,
                'email' => 'employee' . $i . '@app.com',
                'password' => bcrypt('password'),
                'is_active' => true,
                'role_id' => $roleEmployee,
                'theme_preference' => 'light',
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
        }
    }
}
