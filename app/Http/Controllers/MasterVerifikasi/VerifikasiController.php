<?php

namespace App\Http\Controllers\MasterVerifikasi;

use Auth;
use Carbon;
use DataTables;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Models
use App\TmResult;
use App\Models\Time;
use App\Models\Pegawai;
use App\Models\Quesioner;
use App\Models\Indikator;
use App\Models\TrResultFile;
use App\Models\VerifikatorTempat;
use App\Models\TrQuesionerAnswer;

class VerifikasiController extends Controller
{
    protected $route = 'verifikasi.';
    protected $path = 'file/';
    protected $view = 'pages.verifikasi.';

    public function index()
    {
        $title = 'Perangkat Daerah';
        $route = $this->route;

        $time = Carbon\Carbon::now();
        $year = $time->format('Y');

        $tahuns = Time::select('id', 'tahun')->get();

        return view('pages.verifikasi.index', compact(
            'title',
            'route',
            'tahuns',
            'year'
        ));
    }

    public function api(Request $request)
    {
        $tahunId = $request->tahun_id;
        $user_id = Auth::user()->id;

        $tempats = VerifikatorTempat::select('tempat_id')->where('user_id', $user_id)->get()->toArray();

        $results = TmResult::getDataResult($tahunId, $tempats);

        return DataTables::of($results)
            ->editColumn('nama', function ($p) use ($tahunId) {
                $tahun = Time::where('id', $p->tahun_id)->first();

                return "<a href='show?tahun_id=" . $tahunId . "&user_id=" . $p->id . "' class='text-primary' title='Show Data'>" . $p->nama_instansi . " (" . $tahun->tahun . ")</a>";
            })
            ->addColumn('status_verifikasi', function ($p) use ($tahunId) {
                $tahun = Time::where('id', $p->tahun_id)->first();

                $dataCount = TmResult::select('tm_pegawais.nama_instansi', 'tm_results.user_id as id', 'tm_quesioners.tahun_id', 'tm_results.user_id')
                    ->join('tm_users', 'tm_users.id', '=', 'tm_results.user_id')
                    ->join('tm_pegawais', 'tm_pegawais.user_id', '=', 'tm_users.id')
                    ->join('tm_places', 'tm_places.id', '=', 'tm_pegawais.tempat_id')
                    ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
                    ->where('tm_results.user_id', $p->user_id)
                    ->where('tm_quesioners.tahun_id', $tahunId)
                    ->get()
                    ->count();

                $getTotalVerif = TmResult::getTotalVerif($tahun->id, $p->user_id);
                $getPercentVerif = round($getTotalVerif / $dataCount * 100);

                if ($getTotalVerif > 0) {
                    if ($getTotalVerif == $dataCount) {
                        return $getPercentVerif . '%' . " <i class='icon-verified_user text-primary'></i>";
                    } else {
                        return  $getTotalVerif . "/" . $dataCount . "&nbsp;&nbsp; (" . $getPercentVerif . "%)";
                    }
                } else {
                    return "<span class='text-danger font-weight-normal fs-13'>Belum Diverifikasi <i class='icon-info-circle text-danger'></i></span>";
                }
            })
            ->addIndexColumn()
            ->rawColumns(['nama', 'status_verifikasi'])
            ->toJson();
    }

    public function show(Request $request)
    {
        $id = $request->user_id;

        $pegawai = Pegawai::where('user_id', $id)->first();
        $userId  = $id;
        $nTempat = $pegawai->tempat->n_tempat;
        $nKepala = $pegawai->nama_kepala;
        $jKepala = $pegawai->jabatan_kepala;
        $alamat  = $pegawai->alamat;
        $noTelp  = $pegawai->telp;
        $nOperator = $pegawai->nama_operator;
        $jOperator = $pegawai->jabatan_operator;
        $path  = $this->path;
        $email = $pegawai->email;
        $nama_instansi = $pegawai->nama_instansi;
        $route = $this->route;

        $tahunId = $request->tahun_id;
        $year = Time::where('id', $tahunId)->first();
        $tahun = $year->tahun;

        $datas = TmResult::select('tm_results.id as id', 'tm_questions.n_question', 'tm_questions.id as id_question', 'tm_quesioners.id as id_quesioner', 'status', 'answer_id_revisi', 'tm_results.answer_id as answer_id', 'keterangan_revisi')
            ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->join('tm_questions', 'tm_questions.id', '=', 'tm_quesioners.question_id')
            ->where('tm_results.user_id', $userId)
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->get();

        $indikatorArray = TmResult::indikatorArray($userId, $tahunId);
        $indikators = Indikator::whereIn('id', $indikatorArray)->get();

        $questionArray = TmResult::questionArray($userId, $tahunId);

        // ETC
        $countQuesioners = Quesioner::getTotal($tahunId);
        $countResult = TmResult::getTotal($tahunId, $userId);
        $getPercent = round($countResult / $countQuesioners * 100);
        $totalRevisi = TmResult::join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->where('user_id', $userId)
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->where('status_revisi', 1)->count();

        $countResultVerif = TmResult::getTotalVerif($tahunId, $userId);
        $getPercentVerif = round($countResultVerif / $countQuesioners * 100);

        $role_id = Auth::user()->modelHasRole->role_id;
        $title = 'Perangkat Daerah';

        $totalIndikator = Indikator::select('id', 'n_indikator')->get();
        $sesuai = TmResult::join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->where('status', 1)
            ->where('status_kirim', 1)
            ->where('answer_id_revisi', 1)
            ->where('tm_results.user_id', $userId)
            ->count();
        $proses_verifikasi = TmResult::join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->where('status', 0)
            ->where('status_kirim', 1)
            ->where('tm_results.user_id', $userId)
            ->count();
        $tidak_sesuai = TmResult::join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->where('status', 1)
            ->where('status_kirim', 1)
            ->where('answer_id_revisi', 2)
            ->where('tm_results.user_id', $userId)
            ->count();
        $sedang_direvisi = TmResult::join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->whereNotNull('keterangan_revisi')
            ->where('status_kirim', 0)
            ->where('tm_results.user_id', $userId)
            ->count();

        $total_pertanyaan = Quesioner::count();
        $total_pertanyaan_diisi = TmResult::where('tm_results.user_id', $userId)->count();
        $tidak_diisi = $total_pertanyaan - $total_pertanyaan_diisi;

        return view('pages.verifikasi.show', compact(
            'sedang_direvisi',
            'tidak_diisi',
            'sesuai',
            'proses_verifikasi',
            'tidak_sesuai',
            'totalIndikator',
            'totalRevisi',
            'role_id',
            'id',
            'userId',
            'nTempat',
            'nKepala',
            'alamat',
            'noTelp',
            'jKepala',
            'title',
            'route',
            'indikators',
            'tahunId',
            'questionArray',
            'path',
            'nama_instansi',
            'tahun',
            'nOperator',
            'jOperator',
            'email',
            'countQuesioners',
            'countResult',
            'getPercent',
            'countResultVerif',
            'getPercentVerif',
            'datas'
        ));
    }

    // direvisi
    public function updateRevisi($id, Request $request)
    {
        $result = TmResult::where('id', $id)->first();
        $element = $request->element;

        if ($result->status == 1) {
            return redirect()
                ->route('verifikasi.show', array('tahun_id' => $result->quesioner->tahun_id, 'user_id' => $result->user_id))
                ->withSuccess('kuesioner telah diverifikasi.');
        }

        $result->update([
            'keterangan_revisi' => $request->pesan,
            'status_kirim' => 0,
            'status_revisi' => 1
        ]);

        return redirect()
            ->route('verifikasi.show', array('tahun_id' => $result->quesioner->tahun_id, 'user_id' => $result->user_id, '#pertanyaanDiv' . $element))
            ->withSuccess('Berhasil! Quesioner berhasil dikembalikan.');
    }

    public function edit($id, Request $request)
    {
        $data = TmResult::find($id);
        $tahunId = $data->quesioner->tahun_id;
        $tahun = $data->quesioner->tahun->tahun;
        $pegawai = Pegawai::where('user_id', $data->user_id)->first();
        $userId  = $data->user_id;
        $nTempat = $pegawai->tempat->n_tempat;
        $nKepala = $pegawai->nama_kepala;
        $jKepala = $pegawai->jabatan_kepala;
        $alamat  = $pegawai->alamat;
        $noTelp  = $pegawai->telp;
        $nOperator = Auth::user()->pegawai->nama_operator;
        $jOperator = Auth::user()->pegawai->jabatan_operator;
        $nama_instansi = $pegawai->nama_instansi;
        $route = $this->route;
        $path = $this->path;
        $element = $request->element;

        $title = 'Perangkat Daerah';

        $answers = TrQuesionerAnswer::where('quesioner_id', $data->quesioner_id)->get();
        $files = TrResultFile::where('result_id', $data->id)->get();

        if ($data->status == 1) {
            return redirect()
                ->route('verifikasi.show', array('tahun_id' => $data->quesioner->tahun_id, 'user_id' => $data->user_id))
                ->withSuccess('kuesioner telah diverifikasi.');
        }
        return view('pages.verifikasi.edit', compact(
            'userId',
            'nTempat',
            'nKepala',
            'alamat',
            'noTelp',
            'jKepala',
            'title',
            'nama_instansi',
            'tahun',
            'route',
            'data',
            'answers',
            'files',
            'path',
            'tahunId',
            'element'
        ));
    }

    // di update
    public function sendRevisi(Request $request, $id)
    {
        $data = TmResult::find($id);
        $element = $request->element;

        if ($data->status == 1) {
            return redirect()
                ->route('verifikasi.show', array('tahun_id' => $data->quesioner->tahun_id, 'user_id' => $data->user_id, '#' . $element))
                ->withSuccess('kuesioner telah diverifikasi.');
        }

        $data->update([
            'keterangan_revisi' => $request->pesan,
            'answer_id_revisi' => $request->answer_id_revisi
        ]);

        return redirect()
            ->route('verifikasi.show', array('tahun_id' => $data->quesioner->tahun_id, 'user_id' => $data->user_id, '#' . $element))
            ->withSuccess('Data quesioner berhasil diubah.');
    }

    // disetujui
    public function confirm($id, Request $request)
    {
        $result  = TmResult::where('id', $id)->first();
        $element = $request->element;

        if ($result->answer_id_revisi) {
            $result->update([
                'status' => 1
            ]);
        } else {
            $result->update([
                'status' => 1,
                'answer_id_revisi' => $result->answer_id
            ]);
        }

        return redirect()
            ->route('verifikasi.show', array('tahun_id' => $result->quesioner->tahun_id, 'user_id' => $result->user_id, '#pertanyaanDiv' . $element))
            ->withSuccess('Berhasil! Quesioner berhasil diverifikasi.');
    }

    public function batalkanVerifikasi(Request $request, $id)
    {
        $element = $request->element;
        $data = TmResult::find($id);
        $data->update([
            'status' => 0,
            'answer_id_revisi' => null,
            'keterangan_revisi' => null,
            'status_revisi' => null
        ]);

        return redirect()
            ->route('verifikasi.show', array('tahun_id' => $data->quesioner->tahun_id, 'user_id' => $data->user_id, '#' . $element))
            ->withSuccess('Verifikasi kuesioner berhasil dibatalkan.');
    }
}
