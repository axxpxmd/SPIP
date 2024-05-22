<?php

namespace App\Http\Controllers\Quesioner;

use Auth;
use Carbon;
use DateTime;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;

// Models
use App\TmResult;
use App\Models\Time;
use App\Models\Zona;
use App\Models\Answer;
use App\Models\Quesioner;
use App\Models\TrResultFile;

class FormQuesionerController extends Controller
{
    protected $title = 'Quesioner';
    protected $route = 'form-quesioner.';
    protected $view  = 'pages.pengisian.';
    protected $path = 'images/file/';

    public function index()
    {
        $title = $this->title;
        $route = $this->route;

        $tahuns = Time::select('id', 'tahun', 'start', 'end')->get();

        return view($this->view . 'index', compact(
            'tahuns',
            'title',
            'route'
        ));
    }

    public function create(Request $request)
    {
        $request->validate([
            'tahun_id' => 'required|not_in:0'
        ]);

        $zonaId  = Auth::user()->pegawai->tempat->zona_id;

        // Check
        $check = Quesioner::join('tm_indikators', 'tm_indikators.id', '=', 'tm_quesioners.indikator_id')
            ->where('tm_indikators.zona_id', $zonaId)
            ->where('tahun_id', $request->tahun_id)
            ->count();
        $time = Time::select('id', 'tahun', 'start', 'end')->where('id', $request->tahun_id)->first();
        if ($check == 0) {
            return redirect()
                ->route($this->route . 'index')
                ->withErrors('Belum ada quesioner ditahun ' . $time->tahun);
        }

        $userId  = Auth::user()->id;
        $nTempat = Auth::user()->pegawai->tempat->n_tempat;
        $tahunId = $request->tahun_id;
        $nKepala = Auth::user()->pegawai->nama_kepala;
        $jKepala = Auth::user()->pegawai->jabatan_kepala;
        $alamat  = Auth::user()->pegawai->alamat;
        $noTelp  = Auth::user()->pegawai->telp;
        $email   = Auth::user()->pegawai->email;
        $nOperator = Auth::user()->pegawai->nama_operator;
        $jOperator = Auth::user()->pegawai->jabatan_operator;

        // Check
        $check = TmResult::select('tm_quesioners.question_id')
            ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->where('user_id', $userId)
            ->get()->toArray();

        // Get data for quesioners
        $indikators = Quesioner::select('tm_indikators.id', 'tm_quesioners.id as quesionerId', 'indikator_id', 'tm_indikators.n_indikator', 'tm_indikators.deskripsi')
            ->join('tm_indikators', 'tm_indikators.id', '=', 'tm_quesioners.indikator_id')
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->whereNotIn('tm_quesioners.question_id', $check)
            ->where('tm_indikators.zona_id', $zonaId)
            ->groupBy('tm_quesioners.indikator_id')
            ->paginate(1);
        $indikators->appends(['tahun_id' => $tahunId]);
        // ->get();
        $checkQuestion = Quesioner::select('question_id')->get()->toArray();

        // ETC
        $countQuesioners = Quesioner::getTotal($tahunId, $zonaId);
        $countResult = TmResult::getTotal($tahunId, $userId);
        $getPercent = round($countResult / $countQuesioners * 100);

        $getZonaName = Zona::where('id', $zonaId)->first();

        $now = Carbon\Carbon::now();

        // Check Start Time
        $datetime1Start = new DateTime($now->toDateTimeString());
        $datetime2Start = new DateTime($time->start);
        $intervalStart = $datetime1Start->diff($datetime2Start);
        $yearsDiffStart = $intervalStart->format('%r%y');
        $monthDiffStart = $intervalStart->format('%r%m');
        $daysDiffStart = $intervalStart->format('%r%d');
        $hoursDiffStart = $intervalStart->format('%r%h');
        $minutesDiffStart = $intervalStart->format('%r%i');

        // Check End Time
        $datetime1End = new DateTime($now->toDateTimeString());
        $datetime2End = new DateTime($time->end);
        $intervalEnd = $datetime1End->diff($datetime2End);
        $yearsDiffEnd = $intervalEnd->format('%r%y');
        $monthDiffEnd = $intervalEnd->format('%r%m');
        $daysDiffEnd = $intervalEnd->format('%r%d');
        $hoursDiffEnd = $intervalEnd->format('%r%h');
        $minutesDiffEnd = $intervalEnd->format('%r%i');

        return view($this->view . 'form', compact(
            'userId',
            'nTempat',
            'indikators',
            'checkQuestion',
            'nKepala',
            'alamat',
            'noTelp',
            'jKepala',
            'tahunId',
            'time',
            'countQuesioners',
            'getZonaName',
            'daysDiffStart',
            'check',
            'countResult',
            'hoursDiffStart',
            'yearsDiffStart',
            'minutesDiffStart',
            'monthDiffStart',
            'yearsDiffEnd',
            'daysDiffEnd',
            'hoursDiffEnd',
            'minutesDiffEnd',
            'monthDiffEnd',
            'email',
            'nOperator',
            'jOperator',
            'getPercent'
        ));
    }

    public function store(Request $request)
    {
        $totalIndikator = $request->totalIndikator;
        $totalQuestion = $request->totalQuestion;
        $totalPertanyaan = $request->totalPertanyaan;
        $tahun_id = $request->tahun_id;

        DB::beginTransaction(); //* DB Transaction Begin

        for ($i = 0; $i < $totalIndikator; $i++) {
            for ($q = 0; $q < $totalQuestion[$i]; $q++) {
                $answer = 'answer_id' . $i . $q;
                $quesioner = 'quesioner_id' . $i . $q;
                $keterangan = 'keterangan' . $i . $q;

                if ($request->has($answer)) {
                    // get nilai
                    $getNilai = Answer::select('nilai')->where('id', $request->input($answer))->first();

                    $result = new TmResult();
                    $result->user_id = Auth::user()->id;
                    $result->quesioner_id =  $request->input($quesioner);
                    $result->answer_id = $request->input($answer);
                    $result->nilai_akhir = 0;
                    $result->nilai_awal = round($getNilai->nilai / $totalPertanyaan[$i], 2);
                    $result->keterangan = $request->input($keterangan);
                    $result->status_kirim = 0;
                    $result->status = 0;
                    $result->save();
                }

                $getFile = 'file' . $i . $q;
                $checkFile = $request->hasFile($getFile);
                if ($checkFile) {
                    $countFile = count($request->file($getFile));

                    for ($k = 0; $k < $countFile; $k++) {

                        // Saved to Storage
                        $nameFile = 'file' . $i . $q;
                        $file = $request->file($nameFile);

                        // check file size
                        $size = $file[$k]->getSize();
                        if ($size >= 5000000) {
                            DB::rollback(); //* DB Transaction Failed
                            return redirect()
                                ->route('form-quesioner.create', ['tahun_id' => $tahun_id])
                                ->withErrors('Size file terlalu besar, maksimal 5mb per file..');
                        }

                        // set file name
                        $ext = $file[$k]->extension();
                        $fileName = time() . $i . $q . $k . "." . $ext;

                        if (!in_array($ext, ['png', 'jpg', 'jpeg', 'docx', 'pdf', 'PDF', 'zip', 'rar', 'PNG', 'JPG', 'JPEG', 'DOCX', 'ZIP', 'RAR'])) {
                            DB::rollback(); //* DB Transaction Failed
                            return redirect()
                                ->route('form-quesioner.create', ['tahun_id' => $tahun_id])
                                ->withErrors('Extension file tidak diperbolehkan.');
                        }

                        if ($file[$k] != null) {
                            $file[$k]->storeAs($this->path, $fileName, 'sftp', 'public');
                        }

                        // Saved to Table
                        $inputFile = new TrResultFile();
                        $inputFile->result_id = $result->id;
                        $inputFile->file = $fileName;
                        $inputFile->save();
                    }
                }
            }
        }

        DB::commit(); //* DB Transaction Success

        return redirect()
            ->route('form-quesioner.create', ['tahun_id' => $tahun_id])
            ->withSuccess('Quesioner berhasil tersimpan.');
    }
}
