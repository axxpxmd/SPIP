<?php

namespace App\Http\Controllers\MasterData;

use DataTables;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Models
use App\Models\Indikator;
use App\Models\Pertanyaan;

class QuestionController extends Controller
{
    protected $route = 'pertanyaan.';
    protected $view = 'pages.masterData.pertanyaan.';
    protected $title = 'Pertanyaan';

    public function index()
    {
        $route = $this->route;
        $title = $this->title;

        $indikators = Indikator::select('id', 'n_indikator')->get();

        return view($this->view . 'index', compact(
            'route',
            'title',
            'indikators'
        ));
    }

    public function api(Request $request)
    {
        $indikator_id = $request->indikator_id;

        $pertanyaans = Pertanyaan::queryTable($indikator_id);

        return DataTables::of($pertanyaans)
            ->addColumn('action', function ($p) {
                if ($p->questionOnQuesioner->count() == 0) {
                    return "<a href='#' onclick='remove(" . $p->id . ")' class='text-danger' title='Hapus Permission'><i class='icon icon-remove'></i></a>";
                } else {
                    return "-";
                }
            })
            ->editColumn('n_question', function ($p) {
                return "<a href='" . route($this->route . 'show', $p->id) . "' class='text-primary' title='Show Data'>" . substr($p->n_question, 0, 200) . " ...</a>";
            })
            ->editColumn('indikator_id', function ($p) {
                return $p->indikator->n_indikator;
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'n_question'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'indikator_id' => 'required',
            'n_question' => 'required|unique:tm_questions,n_question'
        ]);

        // get param
        $indikator_id = $request->indikator_id;
        $n_question = $request->n_question;

        foreach ($n_question as $key => $i) {
            // save to table
            $data = new Pertanyaan();
            $data->indikator_id = $indikator_id;
            $data->n_question = $n_question[$key];
            $data->save();
        }

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil tersimpan.'
        ]);
    }

    public function destroy($id)
    {
        Pertanyaan::where('id', $id)->delete();

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil dihapus.'
        ]);
    }

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;

        $pertanyaan = Pertanyaan::find($id);
        $indikators = Indikator::select('id', 'n_indikator')->get();

        return view($this->view . 'show', compact(
            'route',
            'title',
            'pertanyaan',
            'indikators'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'indikator_id' => 'required',
            'n_question' => 'required|unique:tm_questions,n_question,' . $id,
            'status_wajib' => 'required'
        ]);

        // get param
        $indikator_id = $request->indikator_id;
        $n_question = $request->n_question;
        $status_wajib = $request->status_wajib;

        Pertanyaan::where('id', $id)->update([
            'indikator_id' => $indikator_id,
            'n_question' => $n_question,
            'status_wajib' => $status_wajib
        ]);

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil diperbaharui.'
        ]);
    }
}
