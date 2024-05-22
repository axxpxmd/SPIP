<?php

namespace App\Http\Controllers\MasterData;

use DataTables;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Models
use App\Models\Time;
use App\Models\Zona;
use App\Models\Answer;
use App\Models\Indikator;
use App\Models\Quesioner;
use App\Models\Pertanyaan;
use App\Models\TrQuesionerAnswer;

class QuesionerController extends Controller
{
    protected $title = 'Quesioner';
    protected $route = 'kuesioner.';
    protected $view = 'pages.masterData.quesioner.';

    public function index()
    {
        $title = $this->title;
        $route = $this->route;

        $tahuns = Time::select('id', 'tahun')->get();
        $indikators = Indikator::select('id', 'n_indikator')->get();
        $indikators = Indikator::select('id', 'n_indikator')->get();

        $zonas = Zona::select('id', 'n_zona')->get();
        $jawabans = Answer::select('id', 'nilai', 'jawaban')->get();

        return view($this->view . 'index', compact(
            'title',
            'route',
            'indikators',
            'tahuns',
            'zonas',
            'jawabans'
        ));
    }

    public function api(Request $request)
    {
        $indikator_id = $request->indikator_id;
        $tahun_id = $request->tahun_id;

        $quesioner = Quesioner::when($tahun_id, function ($q) use ($tahun_id) {
            return $q->where('tahun_id', $tahun_id);
        })
            ->when($indikator_id, function ($q) use ($indikator_id) {
                return $q->where('indikator_id', $indikator_id);
            })
            ->orderBy('id', 'DESC')->get();

        return DataTables::of($quesioner)
            ->addColumn('action', function ($p) {
                return "<a href='#' onclick='remove(" . $p->id . ")' class='text-danger' title='Hapus Permission'><i class='icon icon-remove'></i></a>";
            })
            ->editColumn('question_id', function ($p) {
                return "<a href='" . route($this->route . 'show', $p->id) . "' class='text-primary' title='Show Data'>" . substr($p->question->n_question, 0, 200) . " ...</a>";
            })
            ->editColumn('indikator_id', function ($p) {
                return $p->indikator->n_indikator;
            })
            ->editColumn('tahun_id', function ($p) {
                return $p->tahun->tahun;
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'question_id', 'answer_id'])
            ->toJson();
    }

    public function getJawaban($id)
    {
        $data = Answer::select('id', 'nilai', 'jawaban')->where('id', $id)->first();

        return $data;
    }

    public function getPertanyaan($id)
    {
        $notIn = Quesioner::select('question_id')->get()->toArray();

        $data = Pertanyaan::select('id', 'indikator_id', 'n_question')
            ->whereNotIn('id', $notIn)
            ->where('indikator_id', $id)
            ->get();

        return $data;
    }

    /**
     * * Check and Validation
     */
    public function check(Request $request)
    {
        $request->validate([
            'tahun_id' => 'required',
            'indikator_id' => 'required',
            'question_id' => 'required',
            'total_jawaban' => 'required'
        ], [
            'tahun_id.required' => 'Tahun tidak boleh kosong.',
            'indikator_id.required' => 'Indikator tidak boleh kosong',
            'question_id.required' => 'Pertanyaan tidak boleh kosong',
            'total_jawaban.required' => 'Total Jawaban wajib diisi.'
        ]);

        $tahunId = $request->tahun_id;
        $indikatorId = $request->indikator_id;
        $questionId = $request->question_id;
        $totalJawaban = $request->total_jawaban;

        // check
        $check = Quesioner::where('tahun_id', $tahunId)->where('indikator_id', $indikatorId)->where('question_id', $questionId)->count();
        if ($check > 0) {
            return response()->json([
                'message' => 'Indikator dan Pertanyaan sudah pernah disimpan di tahun tersebut.'
            ], 422);
        }

        return response()->json([
            'tahun_id' => $tahunId,
            'indikator_id' => $indikatorId,
            'question_id' => $questionId,
            'total_jawaban' => $totalJawaban
        ]);
    }

    public function create(Request $request)
    {
        $route = $this->route;
        $title = $this->title;

        $tahunId = $request->tahun_id;
        $totalJawaban = $request->total_jawaban;

        $indikatorId = $request->indikator_id;
        $questionId = $request->question_id;

        $jawabans = Answer::select('id', 'jawaban', 'nilai')->get();

        return view($this->view . 'formJawaban', compact(
            'route',
            'title',
            'tahunId',
            'jawabans',
            'totalJawaban',
            'indikatorId',
            'questionId'
        ));
    }

    public function store(Request $request)
    {
        // Get Params
        $tahun_id = $request->tahun_id;
        $indikator_id = $request->indikator_id;
        $question_id = $request->question_id;
        $total_jawaban = $request->total_jawaban;

        // Validate
        for ($i = 0; $i < $total_jawaban; $i++) {
            $k = $i + 1;

            $request->validate([
                'answer_id' . $i => 'required',
            ], [
                'answer_id' . $i . '.required' => 'jawaban ' . $k . ' wajib diisi.'
            ]);
        }

        /*
         * Tahapan :
         * 1. tm_quesioners
         * 2. tr_quesioner_answers
         */

        // Tahap 1
        $quesioner = new Quesioner();
        $quesioner->tahun_id = $tahun_id;
        $quesioner->indikator_id = $indikator_id;
        $quesioner->question_id = $question_id;
        $quesioner->save();

        // Tahap 2
        for ($i = 0; $i < $total_jawaban; $i++) {
            $data = new TrQuesionerAnswer();
            $data->quesioner_id = $quesioner->id;
            $data->answer_id = $_POST['answer_id' . $i];
            $data->save();
        }

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil tersimpan.'
        ]);
    }

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;

        $quesioner = Quesioner::find($id);

        $jawabans = TrQuesionerAnswer::where('quesioner_id', $quesioner->id)->get();
        $checkDuplikat = TrQuesionerAnswer::where('quesioner_id', $quesioner->id)->groupBy('answer_id')->get()->count();
        $totalJawaban = $jawabans->count();

        $allJawabans = Answer::select('id', 'jawaban', 'nilai')->get();
        $tahuns = Time::select('id', 'tahun')->get();
        $indikators = Indikator::select('id', 'n_indikator')->get();
        $questions = Pertanyaan::select('id', 'n_question')->get();

        return view($this->view . 'show', compact(
            'route',
            'title',
            'quesioner',
            'indikators',
            'jawabans',
            'questions',
            'totalJawaban',
            'allJawabans',
            'checkDuplikat',
            'tahuns'
        ));
    }

    public function update(Request $request, $id)
    {
        // Get Params
        $tahun_id = $request->tahun_id;
        $indikator_id = $request->indikator_id;
        $question_id = $request->question_id;
        $total_jawaban = $request->total_jawaban;

        /*
         * Tahapan :
         * 1. tm_quesioners
         * 2. tr_quesioner_answers
         */

        // Tahap 1
        $getCheck = Quesioner::find($id);
        $indikatorId = $getCheck->indikator_id;
        $questionId = $getCheck->question_id;
        $tahunId = $getCheck->tahun_id;

        if ($indikator_id == $indikatorId && $question_id == $questionId && $tahun_id == $tahunId) {
            Quesioner::where('id', $id)->update([
                'tahun_id' => $tahun_id,
                'indikator_id' => $indikator_id,
                'question_id' => $question_id
            ]);
        } else {
            $check = Quesioner::where('tahun_id', $tahun_id)->where('indikator_id', $indikator_id)->where('question_id', $question_id)->count();
            if ($check == 0) {
                Quesioner::where('id', $id)->update([
                    'tahun_id' => $tahun_id,
                    'indikator_id' => $indikator_id,
                    'question_id' => $question_id
                ]);
            } else {
                return response()->json([
                    'message' => 'Indikator dan Pertanyaan sudah pernah disimpan.'
                ], 422);
            }
        }

        // Tahap 2
        for ($i = 0; $i < $total_jawaban; $i++) {
            $tr_quesioner_answer_id = $_POST['tr_quesioner_answer_id' . $i];

            TrQuesionerAnswer::where('id', $tr_quesioner_answer_id)->update([
                'answer_id' => $_POST['answer_id' . $i]
            ]);
        }

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil diperbaharui.'
        ]);
    }

    public function destroy($id)
    {
        $quesioner = Quesioner::where('id', $id)->first();

        // delete from table tr_quesioner_answers
        TrQuesionerAnswer::where('quesioner_id', $quesioner->id)->delete();

        // delete from table tm_quesioners
        $quesioner->delete();

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil dihapus.'
        ]);
    }
}
