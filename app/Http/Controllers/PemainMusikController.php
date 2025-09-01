<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PemainMusik;
use App\Models\AlatMusik;
use Illuminate\Support\Facades\Storage;

class PemainMusikController extends Controller
{
    public function index() {
        $pemainMusik = PemainMusik::with('alat')->get();
        $alatMusik = AlatMusik::all();
        return view('musikpersonil', compact('pemainMusik', 'alatMusik'));
    }

    public function store(Request $request) {
        $request->validate([
            'nama_pemain' => 'required|string',
            'gender' => 'required|in:L,P',
            'foto' => 'nullable|image',
            'alat' => 'nullable|array',
        ]);

        $pemain = new PemainMusik();
        $pemain->nama_pemain = $request->nama_pemain;
        $pemain->gender = $request->gender;

        if($request->hasFile('foto')){
            $pemain->foto = $request->file('foto')->store('pemain', 'public');
        }

        $pemain->save();

        if($request->alat){
            $pemain->alat()->sync($request->alat);
        }

        return redirect()->back()->with('success', 'Data pemain berhasil ditambahkan');
    }

    public function update(Request $request, $id){
        $pemain = PemainMusik::findOrFail($id);

        $pemain->nama_pemain = $request->nama_pemain;
        $pemain->gender = $request->gender;

        if($request->hasFile('foto')){
            // Hapus foto lama kalau ada
            if($pemain->foto && Storage::disk('public')->exists($pemain->foto)){
                Storage::disk('public')->delete($pemain->foto);
            }

            // Simpan foto baru
            $pemain->foto = $request->file('foto')->store('pemain', 'public');
        }

        $pemain->save();

        if($request->alat){
            $pemain->alat()->sync($request->alat);
        }

        return redirect()->back()->with('success', 'Data pemain berhasil di ubah');
    }

    public function destroy($id){
        $pemain = PemainMusik::findOrFail($id);
        if($pemain->foto && Storage::disk('public')->exists($pemain->foto)){
            Storage::disk('public')->delete($pemain->foto);
        }
        $pemain->delete();
        return redirect()->back()->with('success', 'Data pemain berhasil dihapus');
    }
}
