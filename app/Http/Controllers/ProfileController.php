<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        // Menampilkan halaman profile
        $user = Auth::user();
        return view('profile', compact('user'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\Admin $user */
        $user = auth()->user();

        $request->validate([
            'nama_user' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($request->filled('nama_user')) {
            $user->nama_user = $request->nama_user;
        }

        if ($request->filled('password')) {
            $user->password = $request->password; // otomatis hash di model
        }

        // Upload foto profile
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->storeAs('public/profile', $filename);
            $user->foto = 'profile/'.$filename;
        }

        $user->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbarui');
    }

}
