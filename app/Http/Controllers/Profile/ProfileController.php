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

namespace App\Http\Controllers\Profile;

use Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

// Model
use App\User;
use App\Models\AdminDetail;
use App\Models\Pegawai;

class ProfileController extends Controller
{
    protected $view  = 'pages.profile.';
    protected $path  = 'images/ava/';
    protected $title = 'Profile';
    protected $route = 'profile.';

    public function index()
    {
        $route = $this->route;
        $path  = $this->path;
        $title = $this->title;

        $pegawai = Pegawai::where('user_id', Auth::user()->id)->first();

        return view($this->view . 'index', compact(
            'route',
            'path',
            'title',
            'pegawai'
        ));
    }

    public function update(Request $request, $id)
    {
        $adminId = Auth::user()->id;
        $adminDetail = AdminDetail::findOrFail($id);

        $request->validate([
            'username'  => 'required|max:50|unique:admins,username,' . $adminId,
            'full_name' => 'required|max:100',
            'email' => 'required|max:100|email|unique:admin_details,email,' . $id,
            'phone' => 'required|max:20'
        ]);

        // Get Data
        $username  = $request->username;
        $full_name = $request->full_name;
        $email = $request->email;
        $phone = $request->phone;

        /* Tahapan :
         * 1. admins
         * 2. admin_details
         */

        // Tahap 1
        User::where('id', $adminId)->update([
            'username' => $username
        ]);

        // Tahap 2
        if ($request->photo != null) {
            $request->validate([
                'photo' => 'required|max:2024|mimes:png,jpg,jpeg'
            ]);

            // Proses Saved Foto
            $file     = $request->file('photo');
            $fileName = time() . "." . $file->getClientOriginalName();
            $request->file('photo')->move("images/ava/", $fileName);

            if ($adminDetail->photo != 'default.png') {
                // Proses Delete Foto
                $exist = $adminDetail->photo;
                $path  = "images/ava/" . $exist;
                \File::delete(public_path($path));
            }

            $adminDetail->update([
                'full_name' => $full_name,
                'email' => $email,
                'phone' => $phone,
                'photo' => $fileName
            ]);
        } else {
            $adminDetail->update([
                'full_name' => $full_name,
                'email' => $email,
                'phone' => $phone,
            ]);
        }

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil diperbaharui.'
        ]);
    }

    public function editPassword()
    {
        $route  = $this->route;
        $title  = $this->title;

        $userId = Auth::user()->id;

        return view($this->view . 'edit-password', compact(
            'route',
            'title',
            'userId'
        ));
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password'
        ]);

        $password = $request->password;

        User::where('id', $id)->update([
            'password' => Hash::make($password)
        ]);

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil diperbaharui.'
        ]);
    }
}
