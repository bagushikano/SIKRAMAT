<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\DesaAdat;
use App\Models\DesaDinas;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Provinsi;
use Faker\Factory as Faker;

class DesaAdatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $desas = DesaDinas::get();
        foreach($desas as $desa){
            $new_nama = strtolower($desa->name);
            $new_nama = ucwords($new_nama);
            $desa->name = $new_nama;
            $desa->update();
        }
    }
}