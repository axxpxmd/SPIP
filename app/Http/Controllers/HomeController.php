<?php

namespace App\Http\Controllers;

// Models
use App\User;
use App\TmResult;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('home');
    }

    public function checkDuplicate($jenis)
    {
        if ($jenis == 1) {
            // ngecek
            $user = User::select('id')->get();

            foreach ($user as $i) {
                $check = TmResult::where('user_id', $i->id)
                    ->groupBy('quesioner_id')
                    ->havingRaw('COUNT(quesioner_id) > 1')
                    ->get();

                if (count($check)) {
                    return [
                        'user_id' => $i->id,
                        'data' => $check
                    ];
                }
            }

            return 'ga ada';
        } elseif ($jenis == 2) {
            // hapus
            $user = User::select('id')->get();

            foreach ($user as $i) {
                $check = TmResult::select('id')->where('user_id', $i->id)
                    ->groupBy('quesioner_id')
                    ->havingRaw('COUNT(quesioner_id) > 1')
                    ->get()->toArray();

                if (count($check)) {
                    TmResult::whereIn('id', $check)->delete();
                }
            }

            return 'berhasil hapus data';
        }
    }
}
