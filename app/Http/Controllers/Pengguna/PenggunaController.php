<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of welcome
 *
 * @author Asip Hamdi
 * Github : axxpxmd
 */

namespace App\Http\Controllers\Pengguna;

use DataTables;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

// Model
use App\User;
use App\TmResult;
use App\Models\Tempat;
use App\Models\Pegawai;
use App\Models\ModelHasRole;
use App\Models\VerifikatorTempat;
use Spatie\Permission\Models\Role;

class PenggunaController extends Controller
{
    protected $route = 'pengguna.';
    protected $view  = 'pages.pengguna.';
    protected $path  = 'ava/';
    protected $title = 'Pengguna';

    public function index()
    {
        $route = $this->route;
        $title = $this->title;

        $roles = Role::select('id', 'name')->get();
        $tempat = Tempat::select('id', 'n_tempat')->get();

        return view($this->view . 'index', compact(
            'route',
            'title',
            'roles',
            'tempat'
        ));
    }

    public function api(Request $request)
    {
        $role_id = $request->role_id;

        $pegawai = Pegawai::queryData($role_id);

        return DataTables::of($pegawai)
            ->addColumn('action', function ($p) {
                if ($p->quesionerResult->count() == 0) {
                    return "<a href='#' onclick='remove(" . $p->id . ")' class='text-danger' title='Hapus Permission'><i class='icon icon-times'></i></a>";
                } else {
                    return '-';
                }
            })
            ->editColumn('nama_instansi', function ($p) {
                return "<a href='" . route($this->route . 'show', $p->id) . "' class='text-primary' title='Show Data'>" . $p->nama_instansi . "</a>";
            })
            ->editColumn('tempat', function ($p) {
                if ($p->tempat != null) {
                    return $p->tempat->n_tempat;
                } else {
                    return '-';
                }
            })
            ->editColumn('username', function ($p) {
                return $p->user->username;
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'role', 'nama', 'nama_instansi'])
            ->toJson();
    }

    public function create(Request $request)
    {
        $route = $this->route;
        $title = $this->title;

        $roleId = $request->role_id;
        $tempatId = $request->tempat_id;

        $role = Role::where('id', $roleId)->first();
        $tempat = Tempat::where('id', $tempatId)->first();
        $opds = Tempat::select('id', 'n_tempat')->get();

        if ($roleId ==  null) {
            return redirect()
                ->route($this->route . 'index')
                ->withErrors('Semua form wajid diisi.');
        }

        return view($this->view . 'form_add', compact(
            'route',
            'title',
            'role',
            'roleId',
            'tempatId',
            'tempat',
            'opds'
        ));
    }

    public function store(Request $request)
    {
        if ($request->roleId == 5) {
            $request->validate([
                'username' => 'required|unique:tm_users,username',
                'email' => 'required|email|unique:tm_pegawais,email',
                'nama_instansi' => 'required',
                'nama_kepala' => 'required',
                'nama_operator' => 'required',
                'jabatan_operator' => 'required',
                'telp' => 'required|max:20',
                'alamat' => 'required|max:100',
                'tempatId' => 'required|unique:tm_pegawais,tempat_id'
            ], [
                'tempatId.unique' => 'Instansi ini telah memiliki login.',
            ]);
        }

        // Get Params
        $tempat_id = $request->tempatId;
        $role_id = $request->roleId;
        $username = $request->username;
        $password = '12345678';
        $nama_instansi = $request->nama_instansi;
        $nama_kepala = $request->nama_kepala;
        $jabatan_kepala = $request->jabatan_kepala;
        $nama_operator = $request->nama_operator;
        $jabatan_operator = $request->jabatan_operator;
        $email = $request->email;
        $telp = $request->telp;
        $alamat = $request->alamat;
        $foto = 'default.png';
        $path = 'app\User';
        $opds = $request->opds;

        /* Tahapan :
         * 1. tm_users
         * 2. tm_pegawais
         * 3. model_has_roles
         */

        // Tahap 1
        $user = new User();
        $user->username = $username;
        $user->password = Hash::make($password);
        $user->save();

        // Tahap 2
        $pegawai = new Pegawai();
        $pegawai->user_id = $user->id;
        $pegawai->tempat_id = $tempat_id;
        $pegawai->nama_instansi = $nama_instansi;
        $pegawai->nama_kepala = $nama_kepala;
        $pegawai->jabatan_kepala = $jabatan_kepala;
        $pegawai->nama_operator = $nama_operator;
        $pegawai->jabatan_operator = $jabatan_operator;
        $pegawai->email = $email;
        $pegawai->telp = $telp;
        $pegawai->alamat = $alamat;
        $pegawai->foto = $foto;
        $pegawai->save();

        // Tahap 3
        $model_has_role = new ModelHasRole();
        $model_has_role->role_id = $role_id;
        $model_has_role->model_type = $path;
        $model_has_role->model_id = $user->id;
        $model_has_role->save();

        // Tahap 4
        if ($opds) {
            foreach ($opds as $key => $i) {
                VerifikatorTempat::create([
                    'user_id' => $user->id,
                    'tempat_id' => $i
                ]);
            }
        }

        return response()->json([
            'message' => "Data " . $this->title . " berhasil tersimpan."
        ]);
    }

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;
        $path  = $this->path;

        $pegawai = Pegawai::find($id);
        $verifikatorTempat = VerifikatorTempat::where('user_id', $pegawai->user_id)->get();
        $verifikatorTempatArray = VerifikatorTempat::select('tempat_id')->where('user_id', $pegawai->user_id)->get()->toArray();

        $opds = Tempat::select('id', 'n_tempat')->whereNotIn('id', $verifikatorTempatArray)->get();

        return view($this->view . 'show', compact(
            'route',
            'title',
            'path',
            'pegawai',
            'verifikatorTempat',
            'opds'
        ));
    }

    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::find($id);

        if ($pegawai->user->modelHasRole->role_id == 5) {
            $request->validate([
                'username'  => 'required|max:50|unique:tm_users,username,' . $pegawai->user_id,
                'email' => 'required|max:100|email|unique:tm_pegawais,email,' . $pegawai->id,
                'nama_instansi' => 'required',
                'nama_kepala' => 'required',
                'nama_operator' => 'required',
                'jabatan_operator' => 'required',
                'telp' => 'required|max:20',
                'alamat' => 'required|max:100',
            ]);
        }

        // Get Params
        $username = $request->username;
        $nama_instansi = $request->nama_instansi;
        $nama_kepala = $request->nama_kepala;
        $jabatan_kepala = $request->jabatan_kepala;
        $nama_operator = $request->nama_operator;
        $jabatan_operator = $request->jabatan_operator;
        $email = $request->email;
        $telp = $request->telp;
        $alamat = $request->alamat;

        /* Tahapan :
         * 1. tm_users
         * 2. tm_pegawais
         */

        // Tahap 1
        User::where('id', $pegawai->user_id)->update([
            'username' => $username
        ]);

        // Tahap 2
        $pegawai->update([
            'nama_instansi' => $nama_instansi,
            'nama_kepala' => $nama_kepala,
            'jabatan_kepala' => $jabatan_kepala,
            'nama_operator' => $nama_operator,
            'jabatan_operator' => $jabatan_operator,
            'email' => $email,
            'telp' => $telp,
            'alamat' => $alamat
        ]);

        // Tahap 4
        if ($request->opds) {
            foreach ($request->opds as $key => $i) {
                VerifikatorTempat::create([
                    'user_id' => $pegawai->user_id,
                    'tempat_id' => $i
                ]);
            }
        }

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil diperbaharui.'
        ]);
    }

    public function editPassword($id)
    {
        $route = $this->route;
        $title = $this->title;

        $user = User::find($id);

        return view($this->view . 'editPassword', compact(
            'route',
            'title',
            'user'
        ));
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|same:password'
        ]);

        user::where('id', $id)->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil diperbaharui.'
        ]);
    }

    public function destroy($id)
    {
        Pegawai::where('id', $id)->delete();

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil dihapus.'
        ]);
    }

    public function deleteVerifikatorTempat($id)
    {
        VerifikatorTempat::where('id', $id)->delete();

        return back()
            ->withSuccess('berhasil terhapus.');
    }
}
