<?php

namespace App\Http\Controllers\Quesioner;

use Auth;
use Carbon;
use DateTime;
use DataTables;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;

// Models
use App\TmResult;
use App\Models\Time;
use App\Models\Pegawai;
use App\Models\Quesioner;
use App\Models\Indikator;
use App\Models\TrResultFile;
use App\Models\TrQuesionerAnswer;

class RevisiController extends Controller
{
    protected $route = 'revisi.';
    protected $title = 'Revisi Quesioner';
    protected $path = 'file/';

    public function index()
    {
        $route = $this->route;
        $title = $this->title;

        return view('pages.revisi.index', compact(
            'route',
            'title'
        ));
    }

    public function api(Request $request)
    {
        $user_id = Auth::user()->id;

        $results = TmResult::select('tm_results.id', 'tm_quesioners.tahun_id')
            ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->whereNotNull('keterangan_revisi')
            ->where('status_kirim', 0)
            ->groupBy('tm_quesioners.tahun_id')
            ->where('tm_results.user_id', $user_id)
            ->get();

        return DataTables::of($results)
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
            ->addColumn('status_pengisian', function ($p) use ($user_id) {
                $tahun = Time::where('id', $p->tahun_id)->first();

                $resultsCount = TmResult::select('tm_results.id', 'tm_quesioners.tahun_id')
                    ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
                    ->where('tm_quesioners.tahun_id', $tahun->id)
                    ->where('tm_results.user_id', $user_id)
                    ->get()
                    ->count();

                $countQuesioners = Quesioner::getTotal($tahun->id);
                $getPercent = round($resultsCount / $countQuesioners * 100);

                if ($countQuesioners == $p->count()) {
                    return $getPercent . '%' . " <i class='icon-verified_user text-primary'></i>";
                } else {
                    return $getPercent . '%';
                }
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
            ->rawColumns(['tahun', 'status_verifikasi', 'status_pengisian', 'status_pengiriman'])
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

        $datas = TmResult::select('tm_results.id as id', 'tm_questions.n_question', 'tm_questions.id as id_question', 'tm_quesioners.id as id_quesioner', 'status', 'tm_results.answer_id as answer_id', 'keterangan_revisi', 'status_kirim', 'answer_id_revisi')
            ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->join('tm_questions', 'tm_questions.id', '=', 'tm_quesioners.question_id')
            ->where('tm_results.user_id', $userId)
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->get();

        $indikatorArray = TmResult::indikatorArrayRevisi($userId, $tahunId);
        $indikators = Indikator::whereIn('id', $indikatorArray)->get();

        $questionArray = TmResult::questionArray($userId, $tahunId);

        // ETC
        $countQuesioners = Quesioner::getTotal($tahunId, $zonaId);
        $countResult = TmResult::getTotal($tahunId, $userId);
        $totalRevisi = TmResult::join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->where('user_id', $userId)
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->where('status_revisi', 1)->count();

        $countResultVerif = TmResult::getTotalVerif($tahunId, $userId);

        // Check Verification
        $status_kirim = TmResult::join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->where('tm_results.user_id', $userId)
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->where('status_kirim', 0)
            ->count();

        return view('pages.revisi.show', compact(
            'totalRevisi',
            'route',
            'title',
            'time',
            'indikators',
            'questionArray',
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
            'countResultVerif',
            'tahunId',
            'status_kirim',
            'datas'
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
                ->route('revisi.show', $data->quesioner->tahun_id)
                ->withSuccess('kuesioner telah sudah dikirim, tidak bisa diedit.');
        }

        return view('pages.revisi.edit', compact(
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
                ->route('revisi.show', $data->quesioner->tahun_id)
                ->withSuccess('kuesioner telah sudah dikirim, tidak bisa diedit.');
        }

        // Get Params
        $answer_id  = $request->answer_id;
        $keterangan = $request->keterangan;

        $data->update([
            'answer_id' => $answer_id,
            'keterangan' => $keterangan
        ]);

        DB::beginTransaction(); //* DB Transaction Begin

        $checkFile = $request->hasFile('file');
        if ($checkFile) {
            $countFile = count($request->file('file'));
            for ($k = 0; $k < $countFile; $k++) {

                // Saved to Storage
                $file = $request->file('file');
                $fileName = time() . "." . $file[$k]->getClientOriginalName();
                $ext = $file[$k]->extension();

                if (!in_array($ext, ['png', 'jpg', 'jpeg', 'docx', 'pdf', 'PDF', 'zip', 'rar', 'PNG', 'JPG', 'JPEG', 'DOCX', 'ZIP', 'RAR'])) {
                    DB::rollback(); //* DB Transaction Failed
                    return redirect()
                        ->route('revisi.edit', $data->id)
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

        DB::commit(); //* DB Transaction Success

        return redirect()
            ->route('revisi.edit', $data->id)
            ->withSuccess('Data Quesioner berhasil diperbaharui.');
    }

    public function deleteFile($id)
    {
        $data = TrResultFile::where('id', $id)->first();

        $data->delete();

        return redirect()
            ->route('revisi.edit', $data->result_id)
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
            ->where('tm_results.status_revisi', 1)
            ->update([
                'status_kirim' => 1,
                'keterangan_revisi' => null,
                'answer_id_revisi' => null,
                'status_revisi' => null
            ]);

        return redirect()
            ->route('revisi.index')
            ->withSuccess('Selamat, Quesioner berhasil terkirim!');
    }
}
