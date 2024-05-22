<?php

namespace App\Http\Controllers\MasterData;

use DataTables;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use App\Http\Controllers\Controller;
use App\Models\Quesioner;
// Models
use App\Models\Time;

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
            ->editColumn('start', function ($p) {
                return Carbon::parse($p->start)->format('d M Y | H:i:s');
            })
            ->editColumn('end', function ($p) {
                return Carbon::parse($p->end)->format('d M Y | H:i:s');
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|unique:tm_times,tahun',
            'start' => 'required',
            'end' => 'required'
        ]);

        // get params
        $tahun = $request->tahun;
        $start = $request->start;
        $end = $request->end;

        // convert  
        $timestampsStart = str_replace('T', ' ', $start);
        $timestampsEnd = str_replace('T', ' ', $end);

        $waktu = new Time();
        $waktu->tahun = $tahun;
        $waktu->start = $timestampsStart;
        $waktu->end = $timestampsEnd;
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
            'start' => str_replace(' ', 'T', $waktu->start),
            'end' => str_replace(' ', 'T', $waktu->end),
        );

        return $convert;
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tahun' => 'required|unique:tm_times,tahun,' . $id,
            'start' => 'required',
            'end' => 'required'
        ]);

        // get params
        $tahun = $request->tahun;
        $start = $request->start;
        $end = $request->end;

        // convert
        $timestampsStart = str_replace('T', ' ', $start);
        $timestampsEnd = str_replace('T', ' ', $end);

        $waktu = Time::find($id);
        $waktu->update([
            'tahun' => $tahun,
            'start' => $timestampsStart,
            'end' => $timestampsEnd
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
