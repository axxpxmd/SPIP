<?php

namespace App\Http\Controllers\MasterData;

use DataTables;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

// Models
use App\Models\Indikator;

class IndikatorController extends Controller
{
    protected $route = 'indikator.';
    protected $view = 'pages.masterData.indikator.';
    protected $title = 'Indikator';

    public function index()
    {
        $route = $this->route;
        $title = $this->title;

        return view($this->view . 'index', compact(
            'route',
            'title'
        ));
    }

    public function api(Request $request)
    {
        $indikator = Indikator::select('id', 'n_indikator', 'deskripsi')->with(['pertanyaan'])->orderBy('id', 'DESC')->get();

        return DataTables::of($indikator)
            ->addColumn('action', function ($p) {
                if ($p->pertanyaan->count() != 0) {
                    return '-';
                } else {
                    return "<a href='#' onclick='remove(" . $p->id . ")' class='text-danger' title='Hapus Permission'><i class='icon icon-remove'></i></a>";
                }
            })
            ->editColumn('n_indikator', function ($p) {
                return "<a href='" . route($this->route . 'show', $p->id) . "' class='text-primary' title='Show Data'>" . $p->n_indikator . "</a>";
            })
            ->editColumn('deskripsi', function ($p) {
                if ($p->deskripsi != null) {
                    return $p->deskripsi;
                } else {
                    return '-';
                }
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'n_indikator'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'n_indikator' => 'required'
        ]);

        // get params
        $n_indikator = $request->n_indikator;
        $deskripsi = $request->deskripsi;

        $indikator = new Indikator();
        $indikator->n_indikator = $n_indikator;
        $indikator->deskripsi = $deskripsi;
        $indikator->save();

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil tersimpan.'
        ]);
    }

    public function show($id)
    {
        $title = $this->title;
        $route = $this->route;

        $indikator = Indikator::find($id);

        return view($this->view . 'show', compact(
            'route',
            'title',
            'indikator'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'n_indikator' => 'required'
        ]);

        // get params
        $n_indikator = $request->n_indikator;
        $deskripsi = $request->deskripsi;

        $tempat = Indikator::find($id);
        $tempat->update([
            'n_indikator' => $n_indikator,
            'deskripsi' => $deskripsi
        ]);

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil diperbaharui.'
        ]);
    }

    public function destroy($id)
    {
        Indikator::where('id', $id)->delete();

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil dihapus.'
        ]);
    }
}
