<?php

namespace App\Http\Controllers\MasterStatusPengisian;

use Auth;
use Carbon;
use DataTables;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Models
use App\TmResult;
use App\Models\Time;
use App\Models\Quesioner;
use App\Models\VerifikatorTempat;

class StatuPengisianController extends Controller
{
    protected $route = 'statusPengisian.';
    protected $path = 'images/file/';
    protected $view = 'pages.statusPengisian.';
    protected $title = 'Perangkat Daerah';

    public function index(Request $request)
    {
        $route = $this->route;

        $zona_id = $request->zona_id;
        $title = $this->title;

        $time = Carbon\Carbon::now();
        $year = $time->format('Y');

        $tahuns = Time::select('id', 'tahun')->get();

        return view($this->view . 'index', compact(
            'title',
            'route',
            'tahuns',
            'year',
            'zona_id'
        ));
    }

    public function api(Request $request)
    {
        $tahunId = $request->tahun_id;
        $zona_id = $request->zona_id;

        $user_id = Auth::user()->id;

        $tempats = VerifikatorTempat::select('tempat_id')->where('user_id', $user_id)->get()->toArray();

        $results = TmResult::select('tm_results.id', 'tm_quesioners.tahun_id', 'tm_results.user_id')
            ->join('tm_users', 'tm_users.id', '=', 'tm_results.user_id')
            ->join('tm_pegawais', 'tm_pegawais.user_id', '=', 'tm_users.id')
            ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->join('tm_indikators', 'tm_indikators.id', '=', 'tm_quesioners.indikator_id')
            ->when($tempats, function ($q) use ($tempats) {
                return $q->whereIn('tm_pegawais.tempat_id', $tempats);
            })
            ->where('tm_indikators.zona_id', $zona_id)
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->groupBy('tm_results.user_id')
            ->get();

        return DataTables::of($results)
            ->addColumn('action', function ($p) {
                return '-';
            })
            ->editColumn('nama_instansi', function ($p) use ($tahunId) {
                $tahun = Time::where('id', $p->tahun_id)->first();
                return $p->user->pegawai->nama_instansi . " ( " . $tahun->tahun . " ) ";
            })

            ->addColumn('status_pengisian', function ($p) use ($tahunId, $zona_id) {
                $user_id = $p->user_id;

                $resultsCount = TmResult::select('tm_results.id', 'tm_quesioners.tahun_id')
                    ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
                    ->where('tm_quesioners.tahun_id', $tahunId)
                    ->where('tm_results.user_id', $user_id)
                    ->get()
                    ->count();

                $countQuesioners = Quesioner::getTotal($tahunId, $zona_id);
                $getPercent = round($resultsCount / $countQuesioners * 100);

                if ($countQuesioners == $resultsCount) {
                    return $resultsCount . ' dari ' . $countQuesioners .  " (" . $getPercent . '%' . ")" . " <i class='icon-verified_user text-primary'></i>";
                } else {
                    return $resultsCount . ' dari ' . $countQuesioners . " (" . $getPercent . '%' . ")";
                }
            })
            ->addIndexColumn()
            ->rawColumns(['nama_instansi', 'status_pengisian', 'action'])
            ->toJson();
    }
}
