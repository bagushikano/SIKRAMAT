<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PendudukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
    	// insert data ke table pegawai
        for ($i = 0; $i<25; $i++){
            DB::table('tb_penduduk')->insert([
                'desa_id' => $faker->numberBetween(1,6),
                'agama_id' => 1,
                'profesi_id' => $faker->numberBetween(1,5),
                'pendidikan_id' => $faker->numberBetween(1,9),
                'nik' => $faker->randomNumber($nbDigits = 16, $strict = false),
                'nama' => $faker->name,
                'tempat_lahir' => $faker->streetName,
                'tanggal_lahir' => $faker->date($format = 'Y-m-d', $max = 'now'),
                'jenis_kelamin' => $faker->shuffle('laki-laki, perempuan'),
                'alamat' => $faker->address,
            ]);
            
        }
    }
}