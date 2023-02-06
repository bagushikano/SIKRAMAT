<?php

namespace App\Models;

use App\Helper\Helper;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\FcmToken;
use App\Models\KramaMipil;
use App\Models\CacahKramaMipil;
use App\Models\KelahiranAjuan;
use App\Models\KematianAjuan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notifikasi extends Model
{
    use SoftDeletes;

    protected $table = 'tb_notifikasi';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function banjar_adat()
    {
        return $this->belongsTo(BanjarAdat::class, 'banjar_adat_id', 'id');
    }

    public function notif_kelahiran_ajuan($idUser, $idBanjarAdat, $idAjuanKelahiran, $idKramaMipil, $type) {
        /**
         * param
         * id user = id user yg melakukan pengajuan
         * id banjar adat = id banjar adat mana tempat user melakukan pengajuan
         * id ajuan kelahiran = id dari ajuan kelahiran yg di buat
         * id krama mipil = id krama mipil tempat ajuan
         * type = 0:Ajuan create, 1: ajuan di proses, 2: ajuan tolak, 3: ajuan acc
         */
        $ajuanKelahiran = KelahiranAjuan::where('id', $idAjuanKelahiran)->first();
        $kramaMipil = KramaMipil::where('id', $idKramaMipil)->with('cacah_krama_mipil.penduduk')->where('status', 1)->first();
        $notifikasi = new Notifikasi();

        if ($type == 0) {
            $konten = "Terdapat ajuan data kelahiran baru oleh Krama ".$kramaMipil->cacah_krama_mipil->penduduk->nama." pada tanggal ".Helper::convert_date_to_locale_id($ajuanKelahiran->tanggal_lahir);
            $notifikasi->banjar_adat_id = $idBanjarAdat;
        } else if ($type == 1) {
            $konten = "Ajuan data kelahiran sedang dalam proses oleh Prajuru Banjar pada tanggal ".Helper::convert_date_to_locale_id($notifikasi->created_at);
            $notifikasi->user_id = $idUser;
        } else if ($type == 2) {
            //TODO isi nama anaknya
            $konten = "Ajuan data kelahiran tidak dapat disahkan dengan alasan: ".$ajuanKelahiran->alasan_tolak_ajuan;
            $notifikasi->user_id = $idUser;
        } else if ($type == 3) {
            $konten = "Ajuan data kelahiran telah disahkan oleh Prajuru Banjar pada tanggal ".Helper::convert_date_to_locale_id($notifikasi->created_at);
            $notifikasi->user_id = $idUser;
        }

        $notifikasi->data_id = $idAjuanKelahiran;
        $notifikasi->konten = $konten;
        /**
         * 0 pendataan
         * 1 ajuan
         */
        $notifikasi->jenis = 1;
        $notifikasi->sub_jenis = "kelahiran";
        $notifikasi->save();

        return $notifikasi;
    }

    public function notif_kematian_ajuan($idUser, $idBanjarAdat, $idAjuanKematian, $pendudukName, $type) {
         /**
         * param
         * id user = id user yg melakukan pengajuan
         * id banjar adat = id banjar adat mana tempat user melakukan pengajuan
         * id ajuan kematian = id dari ajuan kelahiran yg di buat
         * id krama mipil = id krama mipil tempat ajuan
         * type = 0:Ajuan create, 1: ajuan di proses, 2: ajuan tolak, 3: ajuan acc
         */
        $ajuanKematian = KematianAjuan::where('id', $idAjuanKematian)->first();
        $notifikasi = new Notifikasi();

        if ($type == 0) {
            $konten = "Terdapat ajuan data kematian baru oleh Krama ".$pendudukName." pada tanggal ".Helper::convert_date_to_locale_id($ajuanKematian->tanggal_kematian);
            $notifikasi->banjar_adat_id = $idBanjarAdat;
        } else if ($type == 1) {
            $konten = "Ajuan data kematian sedang dalam proses oleh Prajuru Banjar pada tanggal ".Helper::convert_date_to_locale_id($notifikasi->created_at);
            $notifikasi->user_id = $idUser;
        } else if ($type == 2) {
            //TODO isi nama krama
            $konten = "Ajuan data kematian tidak dapat disahkan dengan alasan: ".$ajuanKematian->alasan_tolak_ajuan;
            $notifikasi->user_id = $idUser;
        } else if ($type == 3) {
            $konten = "Ajuan data kematian telah disahkan oleh Prajuru Banjar pada tanggal ".Helper::convert_date_to_locale_id($notifikasi->created_at);
            $notifikasi->user_id = $idUser;
        }

        $notifikasi->data_id = $ajuanKematian->id;
        $notifikasi->konten = $konten;
        /**
         * 0 pendataan
         * 1 ajuan
         */
        $notifikasi->jenis = 1;
        $notifikasi->sub_jenis = "kematian";
        $notifikasi->save();

        return $notifikasi;
    }

    public function notif_proses_kelahiran_ajuan($idAjuanKelahiran){
        $ajuanKelahiran = KelahiranAjuan::with('cacah_krama_mipil.penduduk')->find($idAjuanKelahiran);
        $tanggal_proses = Helper::convert_date_to_locale_id($ajuanKelahiran->tanggal_proses);

        $konten = "Ajuan data kelahiran atas nama ".$ajuanKelahiran->cacah_krama_mipil->penduduk->nama." telah diproses pada tanggal ".$tanggal_proses;
        $notifikasi = new Notifikasi();
        $notifikasi->user_id = $ajuanKelahiran->user_id;
        $notifikasi->data_id = $ajuanKelahiran->id;
        $notifikasi->konten = $konten;
        /**
         * 0 pendataan
         * 1 ajuan
         */
        $notifikasi->jenis = 1;
        $notifikasi->sub_jenis = "kelahiran";
        $notifikasi->save();

        return $notifikasi;
    }

    public function notif_tolak_kelahiran_ajuan($idAjuanKelahiran){
        $ajuanKelahiran = KelahiranAjuan::with('cacah_krama_mipil.penduduk')->find($idAjuanKelahiran);
        $tanggal_tolak = Helper::convert_date_to_locale_id($ajuanKelahiran->tanggal_tolak);

        $konten = "Ajuan data kelahiran atas nama ".$ajuanKelahiran->cacah_krama_mipil->penduduk->nama." telah ditolak pada tanggal ".$tanggal_tolak;
        $notifikasi = new Notifikasi();
        $notifikasi->user_id = $ajuanKelahiran->user_id;
        $notifikasi->data_id = $ajuanKelahiran->id;
        $notifikasi->konten = $konten;
        /**
         * 0 pendataan
         * 1 ajuan
         */
        $notifikasi->jenis = 1;
        $notifikasi->sub_jenis = "kelahiran";
        $notifikasi->save();

        return $notifikasi;
    }

    public function notif_sah_kelahiran_ajuan($idAjuanKelahiran){
        $ajuanKelahiran = KelahiranAjuan::with('cacah_krama_mipil.penduduk')->find($idAjuanKelahiran);
        $tanggal_sah = Helper::convert_date_to_locale_id($ajuanKelahiran->tanggal_sah);

        $konten = "Ajuan data kelahiran atas nama ".$ajuanKelahiran->cacah_krama_mipil->penduduk->nama." telah disahkan pada tanggal ".$tanggal_sah;
        $notifikasi = new Notifikasi();
        $notifikasi->user_id = $ajuanKelahiran->user_id;
        $notifikasi->data_id = $ajuanKelahiran->id;
        $notifikasi->konten = $konten;
        /**
         * 0 pendataan
         * 1 ajuan
         */
        $notifikasi->jenis = 1;
        $notifikasi->sub_jenis = "kelahiran";
        $notifikasi->save();

        return $notifikasi;
    }

    public function notif_kematian_krama($idBanjarAdat, $idKematian, $idKramaMipil){
        /**
         * param
         * id banjar adat = id banjar adat mana tempat krama mati
         * id krama mipil = id dari krama yang mati
         * id kematian = id kematian
         */

        $kematian = Kematian::find($idKematian);
        $kramaMipil = KramaMipil::with('cacah_krama_mipil.penduduk')->find($idKramaMipil);
        $tanggal_mati = Helper::convert_date_to_locale_id($kematian->tanggal_kematian);

        $konten = "Krama ".$kramaMipil->cacah_krama_mipil->penduduk->nama." telah meninggal pada tanggal ".$tanggal_mati.". Silahkan pilih tindakan selanjutnya.";
        $notifikasi = new Notifikasi();
        $notifikasi->banjar_adat_id = $idBanjarAdat;
        $notifikasi->data_id = $kematian->id;
        $notifikasi->konten = $konten;
        /**
         * 0 pendataan
         * 1 ajuan
         */
        $notifikasi->jenis = 0;
        $notifikasi->sub_jenis = "kematian";
        $notifikasi->save();

        return $notifikasi;
    }

    public function notif_create_perkawinan_beda_banjar_adat($idBanjarAdatPradana, $idBanjarAdatPurusa, $idPerkawinan){
        /**
         * param
         * id banjar adat = id banjar adat mana tempat krama mati
         * id krama mipil = id dari krama yang mati
         * id kematian = id kematian
         */

        $perkawinan = Perkawinan::find($idPerkawinan);
        $banjarAdatPradana = BanjarAdat::find($idBanjarAdatPradana);
        $banjarAdatPurusa = BanjarAdat::find($idBanjarAdatPurusa);
        $desaAdatPurusa = DesaAdat::find($banjarAdatPurusa->desa_adat_id);
        $tanggal_kawin = Helper::convert_date_to_locale_id($perkawinan->tanggal_perkawinan);

        $konten = "Terdapat ajuan perkawinan dari Banjar Adat ".$banjarAdatPurusa->nama_banjar_adat." Desa Adat ".$desaAdatPurusa->desadat_nama." pada tanggal ".$tanggal_kawin.".";
        $notifikasi = new Notifikasi();
        $notifikasi->banjar_adat_id = $idBanjarAdatPradana;
        $notifikasi->data_id = $perkawinan->id;
        $notifikasi->konten = $konten;
        /**
         * 0 pendataan
         * 1 ajuan
         */
        $notifikasi->jenis = 0;
        $notifikasi->sub_jenis = "perkawinan";
        $notifikasi->save();

        return $notifikasi;
    }

    public function notif_tolak_perkawinan_beda_banjar_adat($idPerkawinan){
        /**
         * param
         * id banjar adat = id banjar adat mana tempat krama mati
         * id krama mipil = id dari krama yang mati
         * id kematian = id kematian
         */

        $perkawinan = Perkawinan::find($idPerkawinan);
        $banjarAdatPradana = BanjarAdat::find($perkawinan->banjar_adat_pradana_id);
        $banjarAdatPurusa = BanjarAdat::find($perkawinan->banjar_adat_purusa_id);
        $desaAdatPurusa = DesaAdat::find($banjarAdatPurusa->desa_adat_id);
        $desaAdatPradana = DesaAdat::find($banjarAdatPradana->desa_adat_id);
        $tanggal_kawin = Helper::convert_date_to_locale_id($perkawinan->tanggal_perkawinan);

        $konten = "Perkawinan dari Banjar Adat ".$banjarAdatPradana->nama_banjar_adat." Desa Adat ".$desaAdatPradana->desadat_nama." pada tanggal ".$tanggal_kawin." belum dapat dikonfirmasi.";
        $notifikasi = new Notifikasi();
        $notifikasi->banjar_adat_id = $perkawinan->banjar_adat_purusa_id;
        $notifikasi->data_id = $perkawinan->id;
        $notifikasi->konten = $konten;
        /**
         * 0 pendataan
         * 1 ajuan
         */
        $notifikasi->jenis = 0;
        $notifikasi->sub_jenis = "perkawinan";
        $notifikasi->save();

        return $notifikasi;
    }

    public function notif_edit_perkawinan_beda_banjar_adat($idPerkawinan){
        /**
         * param
         * id banjar adat = id banjar adat mana tempat krama mati
         * id krama mipil = id dari krama yang mati
         * id kematian = id kematian
         */

        $perkawinan = Perkawinan::find($idPerkawinan);
        $banjarAdatPradana = BanjarAdat::find($perkawinan->banjar_adat_pradana_id);
        $banjarAdatPurusa = BanjarAdat::find($perkawinan->banjar_adat_purusa_id);
        $desaAdatPurusa = DesaAdat::find($banjarAdatPurusa->desa_adat_id);
        $desaAdatPradana = DesaAdat::find($banjarAdatPradana->desa_adat_id);
        $tanggal_kawin = Helper::convert_date_to_locale_id($perkawinan->tanggal_perkawinan);

        $konten = "Perkawinan dari Banjar Adat ".$banjarAdatPurusa->nama_banjar_adat." Desa Adat ".$desaAdatPurusa->desadat_nama." pada tanggal ".$tanggal_kawin." telah diperbaharui.";
        $notifikasi = new Notifikasi();
        $notifikasi->banjar_adat_id = $perkawinan->banjar_adat_pradana_id;
        $notifikasi->data_id = $perkawinan->id;
        $notifikasi->konten = $konten;
        /**
         * 0 pendataan
         * 1 ajuan
         */
        $notifikasi->jenis = 0;
        $notifikasi->sub_jenis = "perkawinan";
        $notifikasi->save();

        return $notifikasi;
    }

    public function notif_konfirmasi_perkawinan_beda_banjar_adat($idPerkawinan){
        /**
         * param
         * id banjar adat = id banjar adat mana tempat krama mati
         * id krama mipil = id dari krama yang mati
         * id kematian = id kematian
         */

        $perkawinan = Perkawinan::find($idPerkawinan);
        $banjarAdatPradana = BanjarAdat::find($perkawinan->banjar_adat_pradana_id);
        $banjarAdatPurusa = BanjarAdat::find($perkawinan->banjar_adat_purusa_id);
        $desaAdatPurusa = DesaAdat::find($banjarAdatPurusa->desa_adat_id);
        $desaAdatPradana = DesaAdat::find($banjarAdatPradana->desa_adat_id);
        $tanggal_kawin = Helper::convert_date_to_locale_id($perkawinan->tanggal_perkawinan);

        $konten = "Perkawinan dari Banjar Adat ".$banjarAdatPradana->nama_banjar_adat." Desa Adat ".$desaAdatPradana->desadat_nama." pada tanggal ".$tanggal_kawin." telah dikonfirmasi.";
        $notifikasi = new Notifikasi();
        $notifikasi->banjar_adat_id = $perkawinan->banjar_adat_purusa_id;
        $notifikasi->data_id = $perkawinan->id;
        $notifikasi->konten = $konten;
        /**
         * 0 pendataan
         * 1 ajuan
         */
        $notifikasi->jenis = 0;
        $notifikasi->sub_jenis = "perkawinan";
        $notifikasi->save();

        return $notifikasi;
    }

    public function notif_create_maperas_beda_banjar_adat($idMaperas){
        /**
         * param
         * id maperas = id maperas
         */
        $maperas = Maperas::find($idMaperas);
        $tanggal_maperas = Helper::convert_date_to_locale_id($maperas->tanggal_maperas);
        $banjarAdatLama = BanjarAdat::find($maperas->banjar_adat_lama_id);
        $desaAdatLama = DesaAdat::find($banjarAdatLama->desa_adat_id);
        $banjarAdatBaru = BanjarAdat::find($maperas->banjar_adat_baru_id);
        $desaAdatBaru = DesaAdat::find($banjarAdatBaru->desa_adat_id);

        $konten = "Terdapat ajuan maperas dari Banjar Adat ".$banjarAdatBaru->nama_banjar_adat." Desa Adat ".$desaAdatBaru->desadat_nama." pada tanggal ".$tanggal_maperas.".";
        $notifikasi = new Notifikasi();
        $notifikasi->banjar_adat_id = $banjarAdatLama->id;
        $notifikasi->data_id = $maperas->id;
        $notifikasi->konten = $konten;
        /**
         * 0 pendataan
         * 1 ajuan
         */
        $notifikasi->jenis = 0;
        $notifikasi->sub_jenis = "maperas";
        $notifikasi->save();

        return $notifikasi;
    }

    public function notif_tolak_maperas_beda_banjar_adat($idMaperas){
        /**
         * param
         * id maperas = id maperas
         */
        $maperas = Maperas::find($idMaperas);
        $tanggal_maperas = Helper::convert_date_to_locale_id($maperas->tanggal_maperas);
        $banjarAdatLama = BanjarAdat::find($maperas->banjar_adat_lama_id);
        $desaAdatLama = DesaAdat::find($banjarAdatLama->desa_adat_id);
        $banjarAdatBaru = BanjarAdat::find($maperas->banjar_adat_baru_id);
        $desaAdatBaru = DesaAdat::find($banjarAdatBaru->desa_adat_id);

        $konten = "Maperas dari Banjar Adat ".$banjarAdatLama->nama_banjar_adat." Desa Adat ".$desaAdatLama->desadat_nama." pada tanggal ".$tanggal_maperas." belum dapat dikonfirmasi.";
        $notifikasi = new Notifikasi();
        $notifikasi->banjar_adat_id = $banjarAdatBaru->id;
        $notifikasi->data_id = $maperas->id;
        $notifikasi->konten = $konten;
        /**
         * 0 pendataan
         * 1 ajuan
         */
        $notifikasi->jenis = 0;
        $notifikasi->sub_jenis = "maperas";
        $notifikasi->save();

        return $notifikasi;
    }

    public function notif_edit_maperas_beda_banjar_adat($idMaperas){
         /**
         * param
         * id maperas = id maperas
         */
        $maperas = Maperas::find($idMaperas);
        $tanggal_maperas = Helper::convert_date_to_locale_id($maperas->tanggal_maperas);
        $banjarAdatLama = BanjarAdat::find($maperas->banjar_adat_lama_id);
        $desaAdatLama = DesaAdat::find($banjarAdatLama->desa_adat_id);
        $banjarAdatBaru = BanjarAdat::find($maperas->banjar_adat_baru_id);
        $desaAdatBaru = DesaAdat::find($banjarAdatBaru->desa_adat_id);

        $konten = "Maperas dari Banjar Adat ".$banjarAdatBaru->nama_banjar_adat." Desa Adat ".$desaAdatBaru->desadat_nama." pada tanggal ".$tanggal_maperas." telah diperbaharui.";
        $notifikasi = new Notifikasi();
        $notifikasi->banjar_adat_id = $banjarAdatLama->id;
        $notifikasi->data_id = $maperas->id;
        $notifikasi->konten = $konten;
        /**
         * 0 pendataan
         * 1 ajuan
         */
        $notifikasi->jenis = 0;
        $notifikasi->sub_jenis = "maperas";
        $notifikasi->save();

        return $notifikasi;
    }

    public function notif_konfirmasi_maperas_beda_banjar_adat($idMaperas){
        /**
         * param
         * id maperas = id maperas
         */
        $maperas = Maperas::find($idMaperas);
        $tanggal_maperas = Helper::convert_date_to_locale_id($maperas->tanggal_maperas);
        $banjarAdatLama = BanjarAdat::find($maperas->banjar_adat_lama_id);
        $desaAdatLama = DesaAdat::find($banjarAdatLama->desa_adat_id);
        $banjarAdatBaru = BanjarAdat::find($maperas->banjar_adat_baru_id);
        $desaAdatBaru = DesaAdat::find($banjarAdatBaru->desa_adat_id);

        $konten = "Maperas dari Banjar Adat ".$banjarAdatLama->nama_banjar_adat." Desa Adat ".$desaAdatLama->desadat_nama." pada tanggal ".$tanggal_maperas." telah dikonfirmasi.";
        $notifikasi = new Notifikasi();
        $notifikasi->banjar_adat_id = $banjarAdatBaru->id;
        $notifikasi->data_id = $maperas->id;
        $notifikasi->konten = $konten;
        /**
         * 0 pendataan
         * 1 ajuan
         */
        $notifikasi->jenis = 0;
        $notifikasi->sub_jenis = "maperas";
        $notifikasi->save();

        return $notifikasi;
    }

    public function notif_create_perceraian_beda_banjar_adat($idPerceraian){
        /**
         * param
         * id perceraian = id perceraian
         */
        $perceraian = Perceraian::find($idPerceraian);
        $banjarAdatPradana = BanjarAdat::find($perceraian->banjar_adat_pradana_id);
        $banjarAdatPurusa = BanjarAdat::find($perceraian->banjar_adat_purusa_id);
        $desaAdatPurusa = DesaAdat::find($banjarAdatPurusa->desa_adat_id);
        $tanggal_cerai = Helper::convert_date_to_locale_id($perceraian->tanggal_perceraian);

        $konten = "Terdapat ajuan data perceraian dari Banjar Adat ".$banjarAdatPurusa->nama_banjar_adat." Desa Adat ".$desaAdatPurusa->desadat_nama." pada tanggal ".$tanggal_cerai.".";
        $notifikasi = new Notifikasi();
        $notifikasi->banjar_adat_id = $banjarAdatPradana->id;
        $notifikasi->data_id = $perceraian->id;
        $notifikasi->konten = $konten;
        /**
         * 0 pendataan
         * 1 ajuan
         */
        $notifikasi->jenis = 0;
        $notifikasi->sub_jenis = "perceraian";
        $notifikasi->save();

        return $notifikasi;
    }

    public function notif_tolak_perceraian_beda_banjar_adat($idPerceraian){
        /**
         * param
         * id banjar adat = id banjar adat mana tempat krama mati
         * id krama mipil = id dari krama yang mati
         * id kematian = id kematian
         */

        $perceraian = Perceraian::find($idPerceraian);
        $banjarAdatPradana = BanjarAdat::find($perceraian->banjar_adat_pradana_id);
        $banjarAdatPurusa = BanjarAdat::find($perceraian->banjar_adat_purusa_id);
        $desaAdatPurusa = DesaAdat::find($banjarAdatPurusa->desa_adat_id);
        $desaAdatPradana = DesaAdat::find($banjarAdatPradana->desa_adat_id);
        $tanggal_cerai = Helper::convert_date_to_locale_id($perceraian->tanggal_perceraian);

        $konten = "Perceraian dengan penduduk dari Banjar Adat ".$banjarAdatPradana->nama_banjar_adat." Desa Adat ".$desaAdatPradana->desadat_nama." pada tanggal ".$tanggal_cerai." belum dapat dikonfirmasi.";
        $notifikasi = new Notifikasi();
        $notifikasi->banjar_adat_id = $perceraian->banjar_adat_purusa_id;
        $notifikasi->data_id = $perceraian->id;
        $notifikasi->konten = $konten;
        /**
         * 0 pendataan
         * 1 ajuan
         */
        $notifikasi->jenis = 0;
        $notifikasi->sub_jenis = "perceraian";
        $notifikasi->save();

        return $notifikasi;
    }

    public function notif_edit_perceraian_beda_banjar_adat($idPerceraian){
        /**
         * param
         * id perceraian = id perceraian
         */
        $perceraian = Perceraian::find($idPerceraian);
        $banjarAdatPradana = BanjarAdat::find($perceraian->banjar_adat_pradana_id);
        $banjarAdatPurusa = BanjarAdat::find($perceraian->banjar_adat_purusa_id);
        $desaAdatPurusa = DesaAdat::find($banjarAdatPurusa->desa_adat_id);
        $tanggal_cerai = Helper::convert_date_to_locale_id($perceraian->tanggal_perceraian);

        $konten = "Ajuan data perceraian dari Banjar Adat ".$banjarAdatPurusa->nama_banjar_adat." Desa Adat ".$desaAdatPurusa->desadat_nama." pada tanggal ".$tanggal_cerai." telah diperbaharui.";
        $notifikasi = new Notifikasi();
        $notifikasi->banjar_adat_id = $banjarAdatPradana->id;
        $notifikasi->data_id = $perceraian->id;
        $notifikasi->konten = $konten;
        /**
         * 0 pendataan
         * 1 ajuan
         */
        $notifikasi->jenis = 0;
        $notifikasi->sub_jenis = "perceraian";
        $notifikasi->save();

        return $notifikasi;
    }

    public function notif_konfirmasi_perceraian_beda_banjar_adat($idPerceraian){
        /**
         * param
         * id banjar adat = id banjar adat mana tempat krama mati
         * id krama mipil = id dari krama yang mati
         * id kematian = id kematian
         */

        $perceraian = Perceraian::find($idPerceraian);
        $banjarAdatPradana = BanjarAdat::find($perceraian->banjar_adat_pradana_id);
        $banjarAdatPurusa = BanjarAdat::find($perceraian->banjar_adat_purusa_id);
        $desaAdatPurusa = DesaAdat::find($banjarAdatPurusa->desa_adat_id);
        $desaAdatPradana = DesaAdat::find($banjarAdatPradana->desa_adat_id);
        $tanggal_cerai = Helper::convert_date_to_locale_id($perceraian->tanggal_perceraian);

        $konten = "Perceraian dengan penduduk dari Banjar Adat ".$banjarAdatPradana->nama_banjar_adat." Desa Adat ".$desaAdatPradana->desadat_nama." pada tanggal ".$tanggal_cerai." telah dikonfirmasi.";
        $notifikasi = new Notifikasi();
        $notifikasi->banjar_adat_id = $perceraian->banjar_adat_purusa_id;
        $notifikasi->data_id = $perceraian->id;
        $notifikasi->konten = $konten;
        /**
         * 0 pendataan
         * 1 ajuan
         */
        $notifikasi->jenis = 0;
        $notifikasi->sub_jenis = "perceraian";
        $notifikasi->save();

        return $notifikasi;
    }
}
