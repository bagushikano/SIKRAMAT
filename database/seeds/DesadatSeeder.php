<?php

use Illuminate\Database\Seeder;
use App\Models\DesaAdat;

class DesadatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $desa_adat = DesaAdat::get();
        foreach($desa_adat as $adat){
            $kec_id = $adat->kecamatan_id;
            $kec_id = '510'.$kec_id.'0';
            $adat->kecamatan_id = $kec_id;
            $adat->update();
        }
    }
}
