<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
    	// insert data ke table pegawai
        for ($i = 0; $i<25; $i++){
            DB::table('tb_tamiu_desa_adat')->insert([
                'desa_id' => $faker->numberBetween(1,6),
                'agama_id' => 1,
                'profesi_id' => $faker->numberBetween(1,5),
                'pendidikan_id' => $faker->numberBetween(1,9),
                'nomor_identitas' => $faker->creditCardNumber,
                'nama' => $faker->name,
                'tempat_lahir' => $faker->streetName,
                'tanggal_lahir' => $faker->date($format = 'Y-m-d', $max = 'now'),
                'jenis_kelamin' => $faker->randomElement($array = array ('laki-laki','perempuan')),
                'alamat' => $faker->address,
            ]);
            
        }
    }
}
