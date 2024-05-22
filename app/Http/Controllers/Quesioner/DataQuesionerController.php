<?php

namespace App\Http\Controllers\Quesioner;

use Auth;
use Carbon;
use DateTime;
use DataTables;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Models
use App\TmResult;
use App\Models\Time;
use App\Models\Answer;
use App\Models\FileLhe;
use App\Models\Pegawai;
use App\Models\Quesioner;
use App\Models\Indikator;
use App\Models\Indikator2023;
use App\Models\TrResultFile;
use App\Models\TrQuesionerAnswer;
use Illuminate\Support\Facades\DB;

class DataQuesionerController extends Controller
{
    protected $route = 'hasil.';
    protected $title = 'Hasil Quesioner';
    protected $path = 'images/file/';

    public function index()
    {
        $route = $this->route;
        $title = $this->title;

        return view('pages.pengisian.hasil', compact(
            'route',
            'title'
        ));
    }

    public function api(Request $request)
    {
        $user_id = Auth::user()->id;
        $zona_id = Auth::user()->pegawai->tempat->zona_id;

        $results = TmResult::select(DB::raw("SUM(tm_results.status) as total_status"), 'tm_results.id', 'tm_quesioners.tahun_id', 'user_id')
            ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->groupBy('tm_quesioners.tahun_id')
            ->where('tm_results.user_id', $user_id)
            ->get();

        return DataTables::of($results)
            ->addColumn('lke', function ($p) {
                $data = "<a href='" . route('verifikasi.cetakReport', array('tahun_id' => $p->tahun_id, 'user_id' => $p->user_id)) . "' target='_blank'><i class='icon icon-print'></i></a>";

                if ($p->total_status == 82) {
                    return $data;
                } else {
                    return '-';
                }
            })
            ->addColumn('rekap_lke', function ($p) {
                $data = "<a href='" . route('verifikasi.cetakRekapLkeUser', array('tahun_id' => $p->tahun_id, 'user_id' => $p->user_id)) . "' target='_blank' class='text-success'><i class='icon icon-print'></i></a>";
                $check = Indikator2023::where('tahun_id', $p->tahun_id)->where('user_id', $p->user_id)->count();

                if ($p->total_status == 82 && $check != 0) {
                    return $data;
                } else {
                    return '-';
                }
            })

            ->editColumn('tahun', function ($p) {
                $tahun = Time::where('id', $p->tahun_id)->first();
                return "<a href='" . route($this->route . 'show', $p->tahun_id) . "' class='text-primary' title='Show Data'>" . $tahun->tahun . "</a>";
            })
            ->addColumn('status_verifikasi', function ($p) use ($user_id) {
                $tahun = Time::where('id', $p->tahun_id)->first();

                $resultsCount = TmResult::select('tm_results.id', 'tm_quesioners.tahun_id')->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
                    ->where('tm_quesioners.tahun_id', $tahun->id)
                    ->where('tm_results.user_id', $user_id)
                    ->get()->count();

                $getTotalVerif = TmResult::getTotalVerif($tahun->id, $user_id);
                $getPercentVerif = round($getTotalVerif / $resultsCount * 100);

                if ($getTotalVerif > 0) {
                    if ($getTotalVerif == $p->count()) {
                        return $getPercentVerif . '%' . " <i class='icon-verified_user text-primary'></i>";
                    } else {
                        return $getPercentVerif . '%';
                    }
                } else {
                    return 'Belum diverifikasi';
                }
            })
            ->addColumn('status_pengisian', function ($p) use ($zona_id, $user_id) {
                $tahun = Time::where('id', $p->tahun_id)->first();

                $resultsCount = TmResult::select('tm_results.id', 'tm_quesioners.tahun_id')
                    ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
                    ->where('tm_quesioners.tahun_id', $tahun->id)
                    ->where('tm_results.user_id', $user_id)
                    ->get()
                    ->count();

                $countQuesioners = Quesioner::getTotal($tahun->id, $zona_id);
                $getPercent = round($resultsCount / $countQuesioners * 100);

                if ($countQuesioners == $p->count()) {
                    return $getPercent . '%' . " <i class='icon-verified_user text-primary'></i>";
                } else {
                    return $getPercent . '%';
                }
            })
            ->addColumn('revisi', function ($p) use ($user_id) {
                $data = TmResult::select('tm_results.id', 'tm_quesioners.tahun_id')
                    ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
                    ->where('tm_quesioners.tahun_id', $p->tahun_id)
                    ->where('tm_results.user_id', $user_id)
                    ->whereNotNull('tm_results.message')
                    ->where('status_kirim', 0)
                    ->count();

                return $data . ' Kuesioner';
            })
            ->addColumn('status_pengiriman', function ($p) use ($user_id) {
                $tahun = Time::where('id', $p->tahun_id)->first();

                $resultsCount = TmResult::select('tm_results.id', 'tm_quesioners.tahun_id')
                    ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
                    ->where('tm_quesioners.tahun_id', $tahun->id)
                    ->where('tm_results.user_id', $user_id)
                    ->where('status_kirim', 0)
                    ->get()
                    ->count();
                if ($resultsCount == 0) {
                    return "<span class='text-success font-weight-normal fs-13'>Sudah Dikirim<i class='icon-verified_user ml-2'></i></span>";
                } else {
                    return "<span class='text-danger font-weight-normal fs-13'>Belum Dikirim<i class='icon-info-circle text-danger ml-2'></i></span>";
                }
            })
            ->addIndexColumn()
            ->rawColumns(['tahun', 'status_verifikasi', 'status_pengisian', 'status_pengiriman', 'lke', 'rekap_lke'])
            ->toJson();
    }

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;
        $path  = $this->path;
        $userId = Auth::user()->id;

        $userId  = Auth::user()->id;
        $nTempat = Auth::user()->pegawai->tempat->n_tempat;
        $zonaId  = Auth::user()->pegawai->tempat->zona_id;
        $nKepala = Auth::user()->pegawai->nama_kepala;
        $jKepala = Auth::user()->pegawai->jabatan_kepala;
        $nOperator = Auth::user()->pegawai->nama_operator;
        $jOperator = Auth::user()->pegawai->jabatan_operator;
        $email = Auth::user()->pegawai->email;
        $alamat  = Auth::user()->pegawai->alamat;
        $noTelp  = Auth::user()->pegawai->telp;

        $time = Time::find($id);
        $tahunId = $time->id;

        $datas = TmResult::select('tm_results.id as id', 'tm_questions.n_question', 'tm_questions.id as id_question', 'tm_quesioners.id as id_quesioner', 'nilai_akhir', 'status', 'tm_results.answer_id as answer_id', 'message', 'status_kirim', 'answer_id_revisi')
            ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->join('tm_questions', 'tm_questions.id', '=', 'tm_quesioners.question_id')
            ->where('tm_results.user_id', $userId)
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->get();

        $indikatorArray = TmResult::indikatorArray($userId, $tahunId);
        $indikators = Indikator::whereIn('id', $indikatorArray)->get();

        $questionArray = TmResult::questionArray($userId, $tahunId);

        $getNilai = TmResult::getNilai($userId, $tahunId);
        $getNilaiVerif = TmResult::getNilaiVerif($userId, $tahunId);

        // ETC
        $countQuesioners = Quesioner::getTotal($tahunId, $zonaId);
        $countResult = TmResult::getTotal($tahunId, $userId);
        $getPercent = round($countResult / $countQuesioners * 100);

        $countResultVerif = TmResult::getTotalVerif($tahunId, $userId);
        $getPercentVerif = round($countResultVerif / $countQuesioners * 100);

        // Check Verification
        $status_kirim = TmResult::join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->where('tm_results.user_id', $userId)
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->where('status_kirim', 0)
            ->count();

        $now = Carbon\Carbon::now();

        // Check End Time
        $datetime1End = new DateTime($now->toDateTimeString());
        $datetime2End = new DateTime($time->end);
        $intervalEnd = $datetime1End->diff($datetime2End);
        $yearsDiffEnd = $intervalEnd->format('%r%y');
        $monthDiffEnd = $intervalEnd->format('%r%m');
        $daysDiffEnd = $intervalEnd->format('%r%d');
        $hoursDiffEnd = $intervalEnd->format('%r%h');
        $minutesDiffEnd = $intervalEnd->format('%r%i');

        $nilai_awal = TmResult::select(DB::raw("sum(nilai_awal) as nilai"))->where('user_id', $userId)->first();

        $file_lhe = FileLhe::where('user_id', $userId)->where('tahun_id', $tahunId)->where('status', 0)->first();
        $file_lhe_tindak_lanjut = FileLhe::where('user_id', $userId)->where('tahun_id', $tahunId)->where('status', 1)->first();

        return view('pages.pengisian.show', compact(
            'file_lhe',
            'route',
            'nilai_awal',
            'title',
            'time',
            'indikators',
            'questionArray',
            'getNilai',
            'userId',
            'nTempat',
            'nKepala',
            'alamat',
            'noTelp',
            'jKepala',
            'path',
            'nOperator',
            'jOperator',
            'email',
            'countQuesioners',
            'countResult',
            'getPercent',
            'countResultVerif',
            'getPercentVerif',
            'tahunId',
            'getNilaiVerif',
            'status_kirim',
            'yearsDiffEnd',
            'daysDiffEnd',
            'hoursDiffEnd',
            'minutesDiffEnd',
            'monthDiffEnd',
            'datas',
            'file_lhe_tindak_lanjut'
        ));
    }

    public function edit($id)
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
        $nama_instansi = $pegawai->nama_instansi;
        $route = $this->route;
        $path = $this->path;
        $title = $this->title;
        $zonaId = $pegawai->tempat->zona_id;

        $answers = TrQuesionerAnswer::where('quesioner_id', $data->quesioner_id)->get();
        $files = TrResultFile::where('result_id', $data->id)->get();

        if ($data->status_kirim == 1) {
            return redirect()
                ->route('hasil.show', $data->quesioner->tahun_id)
                ->withSuccess('kuesioner telah sudah dikirim, tidak bisa diedit.');
        }

        return view('pages.pengisian.edit', compact(
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
            'tahunId'
        ));
    }

    public function update(Request $request, $id)
    {
        $data = TmResult::find($id);

        if ($data->status_kirim == 1) {
            return redirect()
                ->route('hasil.show', $data->quesioner->tahun_id)
                ->withSuccess('kuesioner telah sudah dikirim, tidak bisa diedit.');
        }

        // Get Params
        $answer_id  = $request->answer_id;
        $keterangan = $request->keterangan;
        $total_kuesioner = $request->total_kuesioner;

        $getNilai = Answer::select('nilai')->where('id', $answer_id)->first();

        $data->update([
            'answer_id' => $answer_id,
            'nilai_awal' => round($getNilai->nilai / $total_kuesioner, 2),
            'keterangan' => $keterangan
        ]);

        $checkFile = $request->hasFile('file');
        if ($checkFile) {
            $countFile = count($request->file('file'));
            for ($k = 0; $k < $countFile; $k++) {

                // Saved to Storage
                $file = $request->file('file');
                $ext = $file[$k]->extension();
                $fileName = time() . $k . "." . $ext;

                if (!in_array($ext, ['png', 'jpg', 'jpeg', 'docx', 'pdf', 'zip', 'rar', 'PDF', 'PNG', 'JPG', 'JPEG', 'DOCX', 'ZIP', 'RAR'])) {
                    DB::rollback(); //* DB Transaction Failed
                    return back()
                        ->withErrors('Extension file tidak diperbolehkan.');
                }

                if ($file[$k] != null) {
                    $file[$k]->storeAs($this->path, $fileName, 'sftp', 'public');
                }

                // Saved to Table
                $inputFile = new TrResultFile();
                $inputFile->result_id = $data->id;
                $inputFile->file = $fileName;
                $inputFile->save();
            }
        }

        return redirect()
            ->route('hasil.edit', $data->id)
            ->withSuccess('Data Quesioner berhasil diperbaharui.');
    }

    public function deleteFile($id)
    {
        $data = TrResultFile::where('id', $id)->first();

        $data->delete();

        return redirect()
            ->route('hasil.edit', $data->result_id)
            ->withSuccess('File berhasil terhapus.');
    }

    public function sendQuesioner(Request $request)
    {
        $user_id = $request->user_id;
        $tahun_id = $request->tahun_id;

        TmResult::join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->where('tm_results.user_id', $user_id)
            ->where('tm_quesioners.tahun_id', $tahun_id)
            ->where('tm_results.status_kirim', 0)
            ->update([
                'status_kirim' => 1,
                'message' => null,
                'answer_id_revisi' => null,
                'status_revisi' => null,
                'nilai_akhir' => null
            ]);

        return redirect()
            ->route('hasil.show', $tahun_id)
            ->withSuccess('Selamat, Quesioner berhasil terkirim!');
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
            'status' => 1,
        ]);
        $data->update([
            'file' => $fileName
        ]);

        return redirect()
            ->route('hasil.show', $tahun_id)
            ->withSuccess('File LHE berhasil diupload.');
    }
}
