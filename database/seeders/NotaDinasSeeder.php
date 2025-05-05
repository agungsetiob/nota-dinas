<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class NotaDinasSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');
        $years = collect(range(Carbon::now()->subYears(4)->year, Carbon::now()->year))->shuffle();
        
        for ($i = 0; $i < 50; $i++) {
            DB::table('nota_dinas')->insert([
                'skpd_id' => $faker->numberBetween(1, 5),
                'nomor_nota' => $faker->unique()->numerify('ND-#####'),
                'perihal' => $faker->sentence(6),
                'anggaran' => $faker->optional()->randomFloat(2, 1000000, 50000000),
                'tanggal_pengajuan' => Carbon::create($years[$i % 5], $faker->numberBetween(1, 12), $faker->numberBetween(1, 28)),
                //'status' => $faker->randomElement(['draft', 'proses', 'disetujui', 'ditolak', 'dikembalikan']),
                'status' => 'draft',
                'tahap_saat_ini' => 'skpd',
                'asisten_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
