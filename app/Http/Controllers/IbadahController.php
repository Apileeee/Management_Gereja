<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ibadah;
use App\Models\PeriodeLayanan;
use Carbon\Carbon;

class IbadahController extends Controller
{
    public function index()
    {
        $ibadahs = Ibadah::all();
         $periodes = PeriodeLayanan::pluck('nama_periode');
        return view('ibadah', compact('ibadahs', 'periodes'));
    }

    // Tambah Ibadah
    public function store(Request $request)
    {
        $request->validate([
            'nama_periode' => 'required|string',
            'nama_ibadah' => 'required',
            'deskripsi' => 'nullable',
        ]);

        Ibadah::create([
            'nama_periode' => $request->nama_periode,
            'nama_ibadah' => $request->nama_ibadah,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->back()->with('success', 'Data Ibadah berhasil ditambahkan');
    }

    // Edit Ibadah
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_periode' => 'required|string',
            'nama_ibadah' => 'required',
            'deskripsi' => 'nullable',
        ]);

        $ibadah = Ibadah::findOrFail($id);
        $ibadah->update([
            'nama_periode' => $request->nama_periode,
            'nama_ibadah' => $request->nama_ibadah,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->back()->with('success', 'Data Ibadah berhasil diubah');
    }

    // Hapus Ibadah
    public function destroy($id)
    {
        $ibadah = Ibadah::findOrFail($id);
        $ibadah->delete();
        return redirect()->back()->with('success', 'Data Ibadah berhasil dihapus');
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

        return redirect()->back()->with('success', 'Data Waktu ibadah berhasil disimpan');
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

        return redirect()->back()->with('success', 'Data Waktu ibadah berhasil diperbarui');
    }

    public function destroyWaktu($id)
    {
        $ibadah = Ibadah::findOrFail($id);
        $ibadah->waktu_ibadah = null;
        $ibadah->save();

        return redirect()->back()->with('success', 'Data Waktu ibadah berhasil dihapus');
    }
}

