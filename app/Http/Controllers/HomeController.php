<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Quesioner;
use Carbon;

use Illuminate\Support\Facades\Auth;

// Models
use App\User;
use App\Models\Time;
use App\TmResult;
use Illuminate\Foundation\Auth\User as IlluminateUser;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $role_id = Auth::user()->modelHasRole->role_id;

        $totalPerangkatDaerah = User::join('model_has_roles', 'model_has_roles.model_id', '=', 'tm_users.id')
            ->where('model_has_roles.role_id', 5)->count();

        $totalPengisianKuesioner = TmResult::groupBy('user_id')->get();
        $totalPengisianKuesioner = count($totalPengisianKuesioner);
        $totalKuesionerTerkirim = TmResult::where('status_kirim', 1)->groupBy('user_id')->get();
        $totalKuesionerTerverifikasi = TmResult::where('status_kirim', 1)->where('status', 1)->groupBy('user_id')->count();
        $totalKuesioner = Quesioner::count();

        $listOpdRevisi = TmResult::select('id', 'user_id')
            ->whereNotNull('message')
            ->where('status_kirim', 0)
            ->groupBy('user_id')->get();

        return view('home', compact(
            'totalPerangkatDaerah',
            'role_id',
            'totalPengisianKuesioner',
            'totalKuesionerTerkirim',
            'totalKuesionerTerverifikasi',
            'totalKuesioner',
            'listOpdRevisi'
        ));
    }

    public function test($jenis)
    {
        if ($jenis == 1) {
            $data = TmResult::select('id', 'quesioner_id', 'answer_id')->with(['quesioner'])->get();
            foreach ($data as $i) {
                $indikator_id    = $i->quesioner->indikator_id;
                $totalPertanyaan = Quesioner::where('indikator_id', $indikator_id)->count();

                $getNilai   = Answer::select('nilai')->where('id', $i->answer_id)->first();
                $nilai_awal = round($getNilai->nilai / $totalPertanyaan, 2);

                TmResult::where('id', $i->id)->update([
                    'nilai_awal' => $nilai_awal
                ]);
            }
        } elseif ($jenis == 2) {
            $data = TmResult::select('id', 'quesioner_id', 'answer_id', 'answer_id_revisi')->whereNotNull('answer_id_revisi')->with(['quesioner'])->get();
            foreach ($data as $i) {
                $indikator_id    = $i->quesioner->indikator_id;
                $totalPertanyaan = Quesioner::where('indikator_id', $indikator_id)->count();

                $getNilai   = Answer::select('nilai')->where('id', $i->answer_id_revisi)->first();
                $nilai_akhir = round($getNilai->nilai / $totalPertanyaan, 2);

                TmResult::where('id', $i->id)->update([
                    'nilai_akhir' => $nilai_akhir
                ]);
            }
        }

        return 'berhasil';
    }

    public function checkDuplicate($jenis)
    {
        if ($jenis == 1) {
            // ngecek
            $user = User::select('id')->get();

            foreach ($user as $i) {
                $check = TmResult::where('user_id', $i->id)
                    ->groupBy('quesioner_id')
                    ->havingRaw('COUNT(quesioner_id) > 1')
                    ->get();

                if (count($check)) {
                    return [
                        'user_id' => $i->id,
                        'data' => $check
                    ];
                }
            }

            return 'ga ada';
        } elseif ($jenis == 2) {
            // hapus
            $user = User::select('id')->get();

            foreach ($user as $i) {
                $check = TmResult::select('id')->where('user_id', $i->id)
                    ->groupBy('quesioner_id')
                    ->havingRaw('COUNT(quesioner_id) > 1')
                    ->get()->toArray();

                if (count($check)) {
                    TmResult::whereIn('id', $check)->delete();
                }
            }

            return 'berhasil hapus data';
        }
    }
}
