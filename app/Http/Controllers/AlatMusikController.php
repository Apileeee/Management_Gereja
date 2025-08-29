<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AlatMusik;

class AlatMusikController extends Controller
{
    public function index()
    {
        $alatMusik = AlatMusik::all();
        return view('musikpersonil', compact('alatMusik'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'pemain_id' => 'nullable|exists:pemain_musik,id_pemain'
        ]);

        AlatMusik::create($request->all());
        return redirect()->back()->with('success', 'Alat Musik berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'pemain_id' => 'nullable|exists:pemain_musik,id_pemain'
        ]);

        $alat = AlatMusik::findOrFail($id);
        $alat->update($request->all());
        return redirect()->back()->with('success', 'Alat Musik berhasil diubah');
    }

    public function destroy($id)
    {
        $alat = AlatMusik::findOrFail($id);
        $alat->delete();
        return redirect()->back()->with('success', 'Alat Musik berhasil dihapus');
    }
}
