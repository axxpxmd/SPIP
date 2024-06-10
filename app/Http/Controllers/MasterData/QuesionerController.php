<?php

namespace App\Http\Controllers\MasterData;

use DataTables;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Models
use App\Models\Time;
use App\Models\Answer;
use App\Models\Indikator;
use App\Models\Quesioner;
use App\Models\Pertanyaan;
use App\Models\TrQuesionerAnswer;
use App\TmResult;

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

        $checkIndikator = Quesioner::select('indikator_id')->get()->toArray();
        $indikators = Indikator::select('id', 'n_indikator')->whereNotIn('id', $checkIndikator)->get();
        $indikator_filter = Indikator::select('id', 'n_indikator')->get();

        $jawabans = Answer::select('id', 'jawaban')->get();

        return view($this->view . 'index', compact(
            'title',
            'route',
            'indikators',
            'tahuns',
            'jawabans',
            'indikator_filter'
        ));
    }

    public function api(Request $request)
    {
        $indikator_id = $request->indikator_id;
        $tahun_id = $request->tahun_id;

        $quesioner = Quesioner::getDataForTable($tahun_id, $indikator_id);

        return DataTables::of($quesioner)
            ->addColumn('action', function ($p) {
                // return "<a href='#' onclick='remove(" . $p->id . ")' class='text-danger' title='Hapus Permission'><i class='icon icon-remove'></i></a>";
                return '-';
            })
            ->editColumn('question_id', function ($p) {
                return substr($p->question->n_question, 0, 200);
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

    public function getPertanyaan($id)
    {
        $data = Pertanyaan::select('id', 'indikator_id', 'n_question')
            ->where('indikator_id', $id)
            ->get();

        return $data;
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
        $tahun_id = $request->tahun_id;
        $indikator_id = $request->indikator_id;

        $request->validate([
            'tahun_id' => 'required',
            'indikator_id' => 'required'
        ], [
            'tahun_id.required' => 'Tahun wajib diisi',
            'indikator_id.required' => 'Indikator wajib diisi'
        ]);

        // check indikator
        $checkIndikator =  Quesioner::where('indikator_id', $indikator_id)->first();
        if ($checkIndikator) {
            return response()->json([
                'message' => 'Indikator sudah pernah tersimpan.'
            ], 500);
        }

        // generate ulang
        Quesioner::where('indikator_id', $indikator_id)->delete();

        // get total question
        $totalQuestions = Pertanyaan::select('id', 'indikator_id')->where('indikator_id', $indikator_id)->get();
        foreach ($totalQuestions as $i) {
            $dataQuesinoer = Quesioner::create([
                'tahun_id' => $tahun_id,
                'indikator_id' => $indikator_id,
                'question_id' => $i->id
            ]);

            $dataAnswer = Answer::select('id')->get();
            foreach ($dataAnswer as $a) {
                TrQuesionerAnswer::create([
                    'quesioner_id' => $dataQuesinoer->id,
                    'answer_id' => $a->id
                ]);
            }
        }

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil tersimpan.'
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
