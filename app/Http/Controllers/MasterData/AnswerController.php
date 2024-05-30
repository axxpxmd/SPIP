<?php

namespace App\Http\Controllers\MasterData;

use DataTables;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Models
use App\Models\Answer;
use App\Models\Quesioner;
use App\Models\TrQuesionerAnswer;

class AnswerController extends Controller
{
    protected $route = 'answer.';
    protected $view = 'pages.masterData.answer.';
    protected $title = 'Jawaban';

    public function index()
    {
        $route = $this->route;
        $title = $this->title;

        return view($this->view . 'index', compact(
            'route',
            'title'
        ));
    }

    public function api()
    {
        $answers = Answer::select('id', 'jawaban')->with(['answerOnQuesioner'])->orderBy('id', 'DESC')->get();

        return DataTables::of($answers)
            ->addColumn('action', function ($p) {
                if ($p->answerOnQuesioner->count() != 0) {
                    return "<a href='#' onclick='edit(" . $p->id . ")' class='text-success' title='Edit'><i class='icon icon-edit mr-1'></i></a>";
                } else {
                    return "
                    <a href='#' onclick='remove(" . $p->id . ")' class='text-danger mr-2' title='Hapus'><i class='icon icon-remove'></i></a>
                    <a href='#' onclick='edit(" . $p->id . ")' class='text-success' title='Edit'><i class='icon icon-edit mr-1'></i></a>";
                }
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'jawaban' => 'required|unique:tm_answers,jawaban'
        ]);

        // get param
        $jawawan = $request->jawaban;

        $data = new Answer();
        $data->jawaban = $jawawan;
        $data->save();

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil tersimpan.'
        ]);
    }

    public function edit($id)
    {
        $data = Answer::select('id', 'jawaban')->where('id', $id)->first();

        return $data;
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jawaban' => 'required|unique:tm_answers,jawaban,' . $id
        ]);

        // get param
        $jawawan = $request->jawaban;

        $data = Answer::find($id);
        $data->update([
            'jawaban' => $jawawan
        ]);

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil diperbaharui.'
        ]);
    }

    public function destroy($id)
    {
        Answer::where('id', $id)->delete();

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil dihapus.'
        ]);
    }
}
