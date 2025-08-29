<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AlatMusik;
use App\Models\PemainMusik;

class MusikController extends Controller
{
    public function index()
    {
        $alatMusik = AlatMusik::with('pemain')->get();
        $pemainMusik = PemainMusik::all();
        return view('musikpersonil', compact('alatMusik', 'pemainMusik'));
    }
}
