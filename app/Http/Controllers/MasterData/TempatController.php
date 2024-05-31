<?php

namespace App\Http\Controllers\MasterData;

use DataTables;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Models
use App\Models\Tempat;

class TempatController extends Controller
{
    protected $route = 'perangkat-daerah.';
    protected $view = 'pages.masterData.tempat.';
    protected $title = 'Perangkat Daerah';

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
        $tempats = Tempat::select("n_tempat", "alamat", "id")->orderBy('id', 'DESC')->get();

        return DataTables::of($tempats)
            ->addColumn('action', function ($p) {
                if ($p->pegawai) {
                    return "
                    <a href='#' onclick='edit(" . $p->id . ")' class='text-success' title='Edit'><i class='icon icon-edit mr-1'></i></a>";
                } else {
                    return "
                    <a href='#' onclick='edit(" . $p->id . ")' class='text-success' title='Edit'><i class='icon icon-edit mr-1'></i></a>
                    <a href='#' onclick='remove(" . $p->id . ")' class='text-danger mr-2' title='Hapus'><i class='icon icon-remove'></i></a>";
                }
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'alamat' => 'required',
            'n_tempat' => 'required|max:100'
        ]);

        // get params
        $n_tempat = $request->n_tempat;
        $alamat = $request->alamat;

        $tempat = new Tempat();
        $tempat->n_tempat = $n_tempat;
        $tempat->alamat = $alamat;
        $tempat->save();

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil tersimpan.'
        ]);
    }

    public function edit($id)
    {
        $data = Tempat::where('id', $id)->first();

        return $data;
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'alamat' => 'required',
            'n_tempat' => 'required|max:100'
        ]);

        // get params
        $n_tempat = $request->n_tempat;
        $alamat = $request->alamat;

        $tempat = Tempat::find($id);
        $tempat->update([
            'n_tempat' => $n_tempat,
            'alamat' => $alamat
        ]);

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil diperbaharui.'
        ]);
    }

    public function destroy($id)
    {
        Tempat::where('id', $id)->delete();

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil dihapus.'
        ]);
    }
}
