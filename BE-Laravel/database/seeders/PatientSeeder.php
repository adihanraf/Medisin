<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        for ($i = 1; $i <= 10; $i++) {
            Patient::create([
                'no_rekam_medik' => 'RM' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'nama' => $faker->name(),
                'nik' => $faker->unique()->numerify(str_repeat('#', 16)),
                'alamat' => $faker->address(),
                'jml_kunjungan' => $faker->numberBetween(0, 10),
            ]);
        }
    }
}
