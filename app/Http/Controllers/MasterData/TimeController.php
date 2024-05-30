<?php

namespace App\Http\Controllers\MasterData;

use DataTables;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

// Models
use App\Models\Time;
use App\Models\Quesioner;

class TimeController extends Controller
{
    protected $title = 'Waktu';
    protected $route = 'waktu.';
    protected $view  = 'pages.masterData.waktu.';

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
        $times = Time::orderBy('id', 'DESC')->get();

        return DataTables::of($times)
            ->addColumn('action', function ($p) {
                $check = Quesioner::where('tahun_id', $p->id)->first();
                if ($check != null) {
                    return "<a href='#' onclick='edit(" . $p->id . ")' class='text-success' title='Edit'><i class='icon icon-edit mr-1'></i></a>";
                } else {
                    return "
                    <a href='#' onclick='edit(" . $p->id . ")' class='text-success' title='Edit'><i class='icon icon-edit mr-1'></i></a>
                    <a href='#' onclick='remove(" . $p->id . ")' class='text-danger mr-2' title='Hapus'><i class='icon icon-remove'></i></a>";
                }
            })
            ->editColumn('status', function ($p) {
                return $p->status == 1 ? 'Aktif' : 'Tidak Aktif';
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'status'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|unique:tm_times,tahun'
        ]);

        // get params
        $tahun = $request->tahun;

        $waktu = new Time();
        $waktu->tahun = $tahun;
        $waktu->save();

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil tersimpan.'
        ]);
    }

    public function edit($id)
    {
        $waktu = Time::find($id);

        $convert = array(
            'id' => $waktu->id,
            'tahun' => $waktu->tahun,
            'status' => $waktu->status
        );

        return $convert;
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tahun' => 'required|unique:tm_times,tahun,' . $id
        ]);

        // get params
        $tahun = $request->tahun;
        $status = $request->status;

        $waktu = Time::find($id);
        $waktu->update([
            'tahun' => $tahun,
            'status' => $status
        ]);

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil diperbaharui.'
        ]);
    }


    public function destroy($id)
    {
        Time::where('id', $id)->delete();

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil dihapus.'
        ]);
    }
}
