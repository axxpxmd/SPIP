<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Models
use App\TmResult;
use App\Models\Time;
use App\Models\Pegawai;
use App\Models\Tempat;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tahun_id = $request->tahun_id;

        // Get Tahun
        $arrayYears = TmResult::select('tm_quesioners.tahun_id')->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->groupBy('tm_quesioners.tahun_id')
            ->get()
            ->toArray();
        $tahun = Time::select('tahun')->where('id', $tahun_id)->first();

        $times = Time::select('id', 'tahun')->get();

        $totalUser = Pegawai::whereNotIn('tempat_id', [0])->count();
        $userKelurahan = Pegawai::getTotalUserByZone(1);
        $userSekolah   = Pegawai::getTotalUserByZone(2);
        $userPuskesmas = Pegawai::getTotalUserByZone(3);

        // Kelurahan
        $kelurahanKirim = TmResult::instansiFilled($tahun_id, 1);
        $kelurahan = Tempat::where('zona_id', 1)->count();

        // Sekolah
        $sekolahKirim = TmResult::instansiFilled($tahun_id, 2);
        $sekolah = Tempat::where('zona_id', 2)->count();

        // Puskesmas
        $puskesmasKirim = TmResult::instansiFilled($tahun_id, 3);
        $puskesmas = Tempat::where('zona_id', 3)->count();

        return view('pages.dashboard.dashboard', compact(
            'totalUser',
            'userKelurahan',
            'userSekolah',
            'userPuskesmas',
            'times',
            'tahun',
            'tahun_id',
            'sekolah',
            'sekolahKirim',
            'kelurahanKirim',
            'kelurahan',
            'puskesmas',
            'puskesmasKirim'
        ));
    }
}
