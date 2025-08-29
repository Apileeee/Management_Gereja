<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeriodeLayanan;
use Illuminate\Support\Facades\Auth;

class PeriodeController extends Controller
{
    // Tampilkan data periode
    public function index()
    {
        $periodes = PeriodeLayanan::all();
        return view('dashboard', compact('periodes'));
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        PeriodeLayanan::create([
            'nama_periode' => $request->nama_periode,
            'deskripsi' => $request->deskripsi,
            'id_user' => auth()->user()->id_user,
        ]);

        return redirect()->back()->with('success', 'Data periode berhasil ditambahkan');
    }

    // Update data
    public function update(Request $request, $id)
    {
        $periode = PeriodeLayanan::findOrFail($id);
        $periode->update([
            'nama_periode' => $request->nama_periode,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->back()->with('success', 'Data periode berhasil diperbarui');
    }

    // Hapus data
    public function destroy($id)
    {
        $periode = PeriodeLayanan::findOrFail($id);
        $periode->delete();

        return redirect()->back()->with('success', 'Data periode berhasil dihapus');
    }
}
