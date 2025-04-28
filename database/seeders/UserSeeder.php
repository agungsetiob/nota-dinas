<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Administrator',
            'email' => 'admin@mail.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
            'skpd_id' => null,
            'jabatan' => 'Administrator Sistem',
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'Sekretaris Daerah',
            'email' => 'sekda@mail.com',
            'password' => Hash::make('12345678'),
            'role' => 'sekda',
            'skpd_id' => null,
            'jabatan' => 'Sekretaris Daerah',
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'Bupati',
            'email' => 'bupati@mail.com',
            'password' => Hash::make('12345678'),
            'role' => 'bupati',
            'skpd_id' => null,
            'jabatan' => 'Bupati',
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        for ($i = 1; $i <= 3; $i++) {
            DB::table('users')->insert([
                'name' => 'Asisten ' . $i,
                'email' => 'asisten' . $i . '@mail.com',
                'password' => Hash::make('12345678'),
                'role' => 'asisten',
                'skpd_id' => null,
                'jabatan' => 'Asisten ' . $i,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $skpds = DB::table('skpds')->take(10)->get();
        
        foreach ($skpds as $index => $skpd) {
            DB::table('users')->insert([
                'name' => 'Admin EPENDI ' . ($index + 1),
                'email' => 'skpd' . ($index + 1) . '@mail.com',
                'password' => Hash::make('12345678'),
                'role' => 'skpd',
                'skpd_id' => $skpd->id,
                'jabatan' => 'Admin ' . $skpd->nama_skpd,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}