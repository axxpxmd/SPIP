<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Models
use App\TmResult;
use App\Models\Time;
use App\Models\Pegawai;
use App\Models\Tempat;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        return view('pages.dashboard.dashboard');
    }
}
