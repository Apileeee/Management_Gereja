<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ibadah;
use Carbon\Carbon;

class IbadahController extends Controller
{
    public function index()
    {
        $ibadahs = Ibadah::all();
        return view('ibadah', compact('ibadahs'));
    }

    // Tambah Ibadah
    public function store(Request $request)
    {
        $request->validate([
            'nama_ibadah' => 'required',
            'deskripsi' => 'nullable',
        ]);

        Ibadah::create([
            'nama_ibadah' => $request->nama_ibadah,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->back()->with('success', 'Ibadah berhasil ditambahkan');
    }

    // Edit Ibadah
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_ibadah' => 'required',
            'deskripsi' => 'nullable',
        ]);

        $ibadah = Ibadah::findOrFail($id);
        $ibadah->update([
            'nama_ibadah' => $request->nama_ibadah,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->back()->with('success', 'Ibadah berhasil diperbarui');
    }

    // Hapus Ibadah
    public function destroy($id)
    {
        $ibadah = Ibadah::findOrFail($id);
        $ibadah->delete();
        return redirect()->back()->with('success', 'Ibadah berhasil dihapus');
    }

    // Tambah/Edit Waktu Ibadah
    public function storeWaktu(Request $request)
    {
        $request->validate([
            'id_ibadah' => 'required|exists:ibadah,id',
            'tanggal_ibadah' => 'required|date',
            'waktu_ibadah' => 'required'
        ]);

        $datetime = $request->tanggal_ibadah . ' ' . $request->waktu_ibadah . ':00';

        $ibadah = Ibadah::findOrFail($request->id_ibadah);
        $ibadah->waktu_ibadah = $datetime;
        $ibadah->save();

        return redirect()->back()->with('success', 'Waktu ibadah berhasil disimpan');
    }

    public function updateWaktu(Request $request, $id)
    {
        $request->validate([
            'tanggal_ibadah' => 'required|date',
            'waktu_ibadah' => 'required'
        ]);

        $datetime = $request->tanggal_ibadah . ' ' . $request->waktu_ibadah . ':00';
        $ibadah = Ibadah::findOrFail($id);
        $ibadah->waktu_ibadah = $datetime;
        $ibadah->save();

        return redirect()->back()->with('success', 'Waktu ibadah berhasil diperbarui');
    }

    public function destroyWaktu($id)
    {
        $ibadah = Ibadah::findOrFail($id);
        $ibadah->waktu_ibadah = null;
        $ibadah->save();

        return redirect()->back()->with('success', 'Waktu ibadah berhasil dihapus');
    }
}

