<?php

namespace App\Http\Controllers\MasterVerifikasi;

use Auth;
use Carbon;
use DataTables;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

// Models
use App\User;
use App\TmResult;
use App\Models\Time;
use App\Models\Answer;
use App\Models\FileLhe;
use App\Models\Pegawai;
use App\Models\Quesioner;
use App\Models\Indikator;
use App\Models\Indikator2023;
use App\Models\TrResultFile;
use App\Models\VerifikatorTempat;
use App\Models\TrQuesionerAnswer;

class VerifikasiController extends Controller
{
    protected $route = 'verifikasi.';
    protected $path = 'images/file/';
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
            ->addColumn('lke', function ($p) use ($tahunId) {
                $data = "<a href='" . route('verifikasi.cetakReport', array('tahun_id' => $tahunId, 'user_id' => $p->user_id)) . "' target='_blank'><i class='icon icon-print'></i></a>";

                if ($p->total_status == 82) {
                    return $data;
                } else {
                    return '-';
                }
            })
            ->addColumn('rekap_lke', function ($p) use ($tahunId) {
                $data = "<a href='" . route('verifikasi.inputDataTahunSebelum', array('tahun_id' => $tahunId, 'user_id' => $p->user_id)) . "' target='_blank' class='text-success'><i class='icon icon-print'></i></a>";

                if ($p->total_status == 82) {
                    return $data;
                } else {
                    return '-';
                }
            })
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
            ->rawColumns(['nama', 'status_verifikasi', 'skor_awal', 'skor_akhir', 'lke', 'rekap_lke'])
            ->toJson();
    }

    public static function checkNilaiAkhir($user_id)
    {
        $dataCheck = TmResult::select('id', 'quesioner_id', 'answer_id', 'answer_id_revisi')
            ->whereNotNull('answer_id_revisi')
            ->with(['quesioner'])
            ->where('user_id', $user_id)
            ->get();

        foreach ($dataCheck as $i) {
            $indikator_id    = $i->quesioner->indikator_id;
            $totalPertanyaan = Quesioner::where('indikator_id', $indikator_id)->count();

            $getNilai   = Answer::select('nilai')->where('id', $i->answer_id_revisi)->first();
            $nilai_akhir = round($getNilai->nilai / $totalPertanyaan, 2);

            TmResult::where('id', $i->id)->update([
                'nilai_akhir' => $nilai_akhir
            ]);
        }
    }

    public static function checkNilaiAwal($user_id)
    {
        $data = TmResult::select('id', 'quesioner_id', 'answer_id')
            ->where('user_id', $user_id)
            ->with(['quesioner'])
            ->get();

        foreach ($data as $i) {
            $indikator_id    = $i->quesioner->indikator_id;
            $totalPertanyaan = Quesioner::where('indikator_id', $indikator_id)->count();

            $getNilai   = Answer::select('nilai')->where('id', $i->answer_id)->first();
            $nilai_awal = round($getNilai->nilai / $totalPertanyaan, 2);

            TmResult::where('id', $i->id)->update([
                'nilai_awal' => $nilai_awal
            ]);
        }
    }

    public function show(Request $request)
    {
        $id = $request->user_id;

        $pegawai = Pegawai::where('user_id', $id)->first();
        $userId  = $id;
        $nTempat = $pegawai->tempat->n_tempat;
        $zonaId  = $pegawai->tempat->zona_id;
        $nKepala = $pegawai->nama_kepala;
        $jKepala = $pegawai->jabatan_kepala;
        $alamat  = $pegawai->alamat;
        $noTelp  = $pegawai->telp;
        $nOperator = $pegawai->nama_operator;
        $jOperator = $pegawai->jabatan_operator;
        $path  = $this->path;
        $email = Auth::user()->pegawai->email;
        $nama_instansi = $pegawai->nama_instansi;
        $route = $this->route;

        // check nilai akhir
        $this->checkNilaiAkhir($request->user_id);
        $this->checkNilaiAwal($request->user_id);

        $zonaId = $pegawai->tempat->zona_id;
        if ($zonaId == 1) {
            $title = 'Kelurahan';
            $routeBack = 'kelurahan';
        } elseif ($zonaId == 2) {
            $title = 'Sekolah';
            $routeBack = 'sekolah';
        } elseif ($zonaId == 3) {
            $title = 'Perangkat Daerah';
            $routeBack = 'puskesmas';
        }

        $tahunId = $request->tahun_id;
        $year = Time::where('id', $tahunId)->first();
        $tahun = $year->tahun;

        $datas = TmResult::select('tm_results.id as id', 'tm_questions.n_question', 'tm_questions.id as id_question', 'tm_quesioners.id as id_quesioner', 'nilai_akhir', 'status', 'answer_id_revisi', 'tm_results.answer_id as answer_id', 'message')
            ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->join('tm_questions', 'tm_questions.id', '=', 'tm_quesioners.question_id')
            ->where('tm_results.user_id', $userId)
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->get();

        $getNilai = TmResult::getNilai($userId, $tahunId);
        $getNilaiVerif = TmResult::getNilaiVerif($userId, $tahunId);

        $indikatorArray = TmResult::indikatorArray($userId, $tahunId);
        $indikators = Indikator::whereIn('id', $indikatorArray)->get();
        // $indikators->appends(['tahun_id' => $tahunId, 'user_id' => $id]);

        $questionArray = TmResult::questionArray($userId, $tahunId);

        $getIndikator = Quesioner::groupBy('indikator_t')->orderBy('id', 'ASC')->get();

        // ETC
        $countQuesioners = Quesioner::getTotal($tahunId, $zonaId);
        $countResult = TmResult::getTotal($tahunId, $userId);
        $getPercent = round($countResult / $countQuesioners * 100);

        $countResultVerif = TmResult::getTotalVerif($tahunId, $userId);
        $getPercentVerif = round($countResultVerif / $countQuesioners * 100);

        $nilai_awal = TmResult::select(DB::raw("sum(nilai_awal) as nilai"))->where('user_id', $userId)->first();

        $checkNIlaiAkhir = $this->checkNilai(round($getNilaiVerif->nilai_akhir, PHP_ROUND_HALF_UP, 2));
        $checkNilaiAwal = $this->checkNilai(round($nilai_awal->nilai, PHP_ROUND_HALF_UP, 2));

        $file_lhe = FileLhe::where('user_id', $userId)->where('tahun_id', $tahunId)->where('status', 0)->first();
        $file_lhe_tindak_lanjut = FileLhe::where('user_id', $userId)->where('tahun_id', $tahunId)->where('status', 1)->first();

        $role_id = Auth::user()->modelHasRole->role_id;

        return view('pages.verifikasi.show', compact(
            'role_id',
            'file_lhe',
            'checkNIlaiAkhir',
            'checkNilaiAwal',
            'id',
            'userId',
            'nTempat',
            'nKepala',
            'alamat',
            'noTelp',
            'jKepala',
            'title',
            'route',
            'getNilai',
            'indikators',
            'tahunId',
            'questionArray',
            'path',
            'nama_instansi',
            'tahun',
            'routeBack',
            'nOperator',
            'jOperator',
            'email',
            'countQuesioners',
            'countResult',
            'getPercent',
            'countResultVerif',
            'getPercentVerif',
            'getNilaiVerif',
            'datas',
            'nilai_awal',
            'getIndikator',
            'file_lhe_tindak_lanjut'
        ));
    }

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
            'message' => $request->pesan,
            'status_kirim' => 0
        ]);

        return redirect()
            ->route('verifikasi.show', array('tahun_id' => $result->quesioner->tahun_id, 'user_id' => $result->user_id, '#pertanyaanDiv' . $element))
            ->withSuccess('Berhasil! Quesioner berhasil dikembalikan.');
    }

    public function confirm($id, Request $request)
    {
        $result  = TmResult::where('id', $id)->first();
        $element = $request->element;

        if ($result->status_revisi != 1) {
            $result->update([
                'status' => 1,
                'nilai_akhir' => $result->nilai_awal
            ]);
        } else {
            $result->update([
                'status' => 1,
            ]);
        }

        return redirect()
            ->route('verifikasi.show', array('tahun_id' => $result->quesioner->tahun_id, 'user_id' => $result->user_id, '#pertanyaanDiv' . $element))
            ->withSuccess('Berhasil! Quesioner berhasil diverifikasi.');
    }

    public function edit($id, Request $request)
    {
        $data = TmResult::find($id);
        $tahunId = $data->quesioner->tahun_id;
        $tahun = $data->quesioner->tahun->tahun;
        $pegawai = Pegawai::where('user_id', $data->user_id)->first();
        $userId  = $data->user_id;
        $nTempat = $pegawai->tempat->n_tempat;
        $zonaId  = $pegawai->tempat->zona_id;
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

        $zonaId = $pegawai->tempat->zona_id;
        if ($zonaId == 1) {
            $title = 'Kelurahan';
            $routeBack = 'show';
        } elseif ($zonaId == 2) {
            $title = 'Sekolah';
            $routeBack = 'show';
        } elseif ($zonaId == 3) {
            $title = 'Perangkat Daerah';
            $routeBack = 'show';
        }

        $getNilai = TmResult::select(DB::raw("sum(tm_answers.nilai) as nilai"), 'tm_quesioners.indikator_id')
            ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->join('tm_answers', 'tm_answers.id', '=', 'tm_results.answer_id')
            ->where('user_id', $userId)
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->first();

        $total_pertanyaan = Quesioner::where('indikator_id', $data->quesioner->indikator_id)->count();

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
            'getNilai',
            'data',
            'answers',
            'files',
            'path',
            'routeBack',
            'tahunId',
            'total_pertanyaan',
            'element'
        ));
    }

    public function sendRevisi(Request $request, $id)
    {
        $request->validate([
            'nilai_akhir' => 'required'
        ]);

        $data = TmResult::find($id);
        $element = $request->element;

        if ($data->status == 1) {
            return redirect()
                ->route('verifikasi.show', array('tahun_id' => $data->quesioner->tahun_id, 'user_id' => $data->user_id, '#' . $element))
                ->withSuccess('kuesioner telah diverifikasi.');
        }

        $data->update([
            'message' => $request->pesan,
            'nilai_akhir' => $request->nilai_akhir != 0 ? round($request->nilai_akhir, 2) : $data->nilai_awal,
            'status_revisi' => 1,
            'answer_id_revisi' => $request->answer_id_revisi
        ]);

        // check nilai akhir
        $this->checkNilaiAkhir($data->user_id);

        return redirect()
            ->route('verifikasi.show', array('tahun_id' => $data->quesioner->tahun_id, 'user_id' => $data->user_id, '#' . $element))
            ->withSuccess('Data quesioner berhasil diubah.');
    }

    public function report(Request $request)
    {
        $tahunId = $request->tahun_id;
        $zonaId = $request->zona_id;
        $userId = $request->user_id;

        $check = TmResult::indikatorArray($userId, $tahunId);

        $indikators = Quesioner::select('tm_indikators.id', 'tm_quesioners.id as quesionerId', 'indikator_id', 'tm_indikators.n_indikator', 'tm_indikators.deskripsi')
            ->join('tm_indikators', 'tm_indikators.id', '=', 'tm_quesioners.indikator_id')
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->whereIn('tm_indikators.id', $check)
            ->where('tm_indikators.zona_id', $zonaId)
            ->groupBy('tm_quesioners.indikator_id')
            // ->paginate(1);
            ->get();

        $dataUser = Pegawai::where('user_id', $userId)->first();

        $checkQuestion = TmResult::select('tm_quesioners.question_id')
            ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->where('user_id', $userId)
            ->get()->toArray();

        $getNilai = TmResult::getNilai($userId, $tahunId);
        $getNilaiVerif = TmResult::getNilaiVerif($userId, $tahunId);

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView($this->view . 'report', compact(
            'indikators',
            'tahunId',
            'checkQuestion',
            'dataUser',
            'userId',
            'getNilai',
            'getNilaiVerif'
        ))->setPaper('a4', 'landscape');

        return $pdf->download("Report " . $dataUser->tempat->n_tempat . ".pdf");
    }

    public static function checkPengaliBobot($nilai, $pengali_bobot)
    {
        $getValueArray  = \str_replace(['"', '[', ']'], '', $pengali_bobot);
        $convertToArray = explode(',', $getValueArray);

        if ($nilai == 'AA') {
            return $convertToArray[0];
        } elseif ($nilai == 'A') {
            return $convertToArray[1];
        } elseif ($nilai == 'BB') {
            return $convertToArray[2];
        } elseif ($nilai == 'B') {
            return $convertToArray[3];
        } elseif ($nilai == 'CC') {
            return $convertToArray[4];
        } elseif ($nilai == 'C') {
            return $convertToArray[5];
        } elseif ($nilai == 'D') {
            return $convertToArray[6];
        } elseif ($nilai == 'E') {
            return $convertToArray[7];
        }
    }

    public static function checkBobotPertanyaan($nilai, $bobot, $pengali_bobot)
    {
        $getValueArray  = \str_replace(['"', '[', ']'], '', $pengali_bobot);
        $convertToArray = explode(',', $getValueArray);

        if ($nilai == 'AA') {
            return $convertToArray[0] * $bobot;
        } elseif ($nilai == 'A') {
            return $convertToArray[1] * $bobot;
        } elseif ($nilai == 'BB') {
            return $convertToArray[2] * $bobot;
        } elseif ($nilai == 'B') {
            return $convertToArray[3] * $bobot;
        } elseif ($nilai == 'CC') {
            return $convertToArray[4] * $bobot;
        } elseif ($nilai == 'C') {
            return $convertToArray[5] * $bobot;
        } elseif ($nilai == 'D') {
            return $convertToArray[6] * $bobot;
        } elseif ($nilai == 'E') {
            return $convertToArray[7] * $bobot;
        }
    }

    public static function checkNilaiBlade($nilai)
    {
        $nilai = floor($nilai);

        if ($nilai >= 100) {
            return 'AA';
        } elseif ($nilai < 100 && $nilai >= 90) {
            return 'A';
        } elseif ($nilai < 90 && $nilai >= 80) {
            return  'BB';
        } elseif ($nilai < 80 && $nilai >= 70) {
            return 'B';
        } elseif ($nilai < 70 && $nilai >= 60) {
            return 'CC';
        } elseif ($nilai < 60 && $nilai >= 50) {
            return 'C';
        } elseif ($nilai < 50 && $nilai >= 30) {
            return 'D';
        } elseif ($nilai < 30 && $nilai >= 0) {
            return 'E';
        }
    }

    public static function checkNilaiBladeRekap($nilai)
    {
        if ($nilai > 90 && $nilai <= 100) {
            return 'AA';
        } elseif ($nilai > 80 && $nilai <= 90) {
            return 'A';
        } elseif ($nilai > 70 && $nilai <= 80) {
            return 'BB';
        } elseif ($nilai > 60 && $nilai <= 70) {
            return 'B';
        } elseif ($nilai > 50 && $nilai <= 60) {
            return 'CC';
        } elseif ($nilai > 30 && $nilai <= 50) {
            return 'C';
        } elseif ($nilai > 0 && $nilai <= 30) {
            return 'D';
        }
    }

    public function checkNilai($nilai)
    {
        $nilai = floor($nilai);

        if ($nilai >= 100) {
            return 'AA';
        } elseif ($nilai < 100 && $nilai >= 90) {
            return 'A';
        } elseif ($nilai < 90 && $nilai >= 80) {
            return  'BB';
        } elseif ($nilai < 80 && $nilai >= 70) {
            return 'B';
        } elseif ($nilai < 70 && $nilai >= 60) {
            return 'CC';
        } elseif ($nilai < 60 && $nilai >= 50) {
            return 'C';
        } elseif ($nilai < 50 && $nilai >= 30) {
            return 'D';
        } elseif ($nilai < 30 && $nilai >= 0) {
            return 'E';
        }
    }

    public function batalkanVerifikasi(Request $request, $id)
    {
        $element = $request->element;
        $data = TmResult::find($id);
        $data->update([
            'status' => 0,
            'nilai_akhir' => null,
            'answer_id_revisi' => null,
            'message' => null,
            'status_revisi' => null
        ]);

        return redirect()
            ->route('verifikasi.show', array('tahun_id' => $data->quesioner->tahun_id, 'user_id' => $data->user_id, '#' . $element))
            ->withSuccess('Verifikasi kuesioner berhasil dibatalkan.');
    }

    public function cetakReport(Request $request)
    {
        $tahun_id = $request->tahun_id;
        $user_id  = $request->user_id;

        $data_user = User::find($user_id);
        $waktu = Time::find($tahun_id);

        $indikator = Quesioner::groupBy('indikator_t')->orderBy('id', 'ASC')->get();

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView($this->view . 'report', compact(
            'tahun_id',
            'user_id',
            'data_user',
            'waktu',
            'indikator'
        ))->setPaper('a4', 'landscape');

        return $pdf->stream("LKE " . $data_user->pegawai->nama_instansi  . " " . $waktu->tahun . ".pdf");
    }

    public function cetakRekapLke(Request $request)
    {
        $tahun_id = $request->tahun_id;
        $user_id  = $request->user_id;
        $n_indikator = $request->n_indikator;
        $nilai = $request->nilai;

        foreach ($n_indikator as $key => $i) {
            $data = Indikator2023::firstOrCreate([
                'tahun_id' => $tahun_id,
                'user_id' => $user_id,
                'n_indikator' => $n_indikator[$key]
            ]);

            $data->update([
                'nilai' => $nilai[$key]
            ]);
        }

        $data_user = User::find($user_id);
        $waktu = Time::find($tahun_id);

        $indikator = Quesioner::groupBy('indikator_t')->orderBy('id', 'ASC')->get();

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView($this->view . 'rekap', compact(
            'tahun_id',
            'user_id',
            'data_user',
            'waktu',
            'indikator'
        ))->setPaper('a4', 'landscape');

        return $pdf->stream("Rekap LKE " . $data_user->pegawai->nama_instansi  . " " . $waktu->tahun . ".pdf");
    }

    public function cetakRekapLkeUser(Request $request)
    {
        $tahun_id = $request->tahun_id;
        $user_id  = $request->user_id;
        $n_indikator = $request->n_indikator;
        $nilai = $request->nilai;

        $data_user = User::find($user_id);
        $waktu = Time::find($tahun_id);

        $indikator = Quesioner::groupBy('indikator_t')->orderBy('id', 'ASC')->get();

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView($this->view . 'rekap', compact(
            'tahun_id',
            'user_id',
            'data_user',
            'waktu',
            'indikator'
        ))->setPaper('a4', 'landscape');

        return $pdf->stream("Rekap LKE " . $data_user->pegawai->nama_instansi  . " " . $waktu->tahun . ".pdf");
    }

    public function inputDataTahunSebelum(Request $request)
    {
        $title = 'Perangkat Daerah';
        $route = $this->route;

        $tahun_id = $request->tahun_id;
        $user_id  = $request->user_id;

        $user = User::find($user_id);

        $indikator = Quesioner::groupBy('indikator_t')->orderBy('id', 'ASC')->get();

        return view('pages.verifikasi.input-data', compact(
            'title',
            'route',
            'indikator',
            'tahun_id',
            'user_id',
            'user'
        ));
    }

    public function uploadLhe(Request $request)
    {
        $user_id = $request->user_id;
        $tahun_id = $request->tahun_id;
        $file = $request->file;

        // upload file
        $ext = $file->extension();
        $fileName = time() . $user_id . "." . $ext;
        $file->storeAs($this->path, $fileName, 'sftp', 'public');

        $data = FileLhe::firstOrCreate([
            'user_id' => $user_id,
            'tahun_id' => $tahun_id,
            'status' => 0,
        ]);
        $data->update([
            'file' => $fileName
        ]);

        return redirect()
            ->route('verifikasi.show', array('tahun_id' => $tahun_id, 'user_id' => $user_id))
            ->withSuccess('File LHE berhasil diupload.');
    }
}
