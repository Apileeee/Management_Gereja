<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeriodeLayanan;
use App\Models\Ibadah;
use App\Models\PemainMusik;
use App\Models\JadwalIbadah;
use Illuminate\Support\Facades\DB;

class GenerateJadwalController extends Controller
{
    private $populationSize = 10;
    private $generations = 5;
    private $minPersonil = 2;
    private $maxPersonil = 4;

    public function index(Request $request)
    {
        $periodes = PeriodeLayanan::all();
        $selectedPeriode = $request->periode;
        $jadwal = [];
        $generationStats = null;

        // Ambil semua jadwal yang sudah tersimpan (eager load ibadah agar relasi tidak null di blade)
        $jadwalTersimpan = JadwalIbadah::with('ibadah')->orderBy('waktu_ibadah', 'asc')->get();

        if ($selectedPeriode) {
            $periode = PeriodeLayanan::find($selectedPeriode);
            if (!$periode) {
                return redirect()->back()->with('error', 'Periode tidak ditemukan!');
            }

            $ibadahs = Ibadah::where('periode', $periode->id_periode)->get();
            $pemain = PemainMusik::with('alat')->get();

            if ($ibadahs->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada data ibadah untuk periode ini!');
            }

            if ($pemain->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada data pemain musik!');
            }

            $start = microtime(true);

            // 1. Generate Populasi
            $population = $this->generatePopulation($ibadahs, $pemain);

            // 2-4. Evolusi
            for ($gen = 0; $gen < $this->generations; $gen++) {
                $fitnessScores = $this->evaluatePopulation($population, $pemain);
                $population = $this->crossoverPopulation($population, $fitnessScores);
                $population = $this->mutatePopulation($population, $pemain);
            }

            // 5. Ambil kandidat terbaik
            $finalFitness = $this->evaluatePopulation($population, $pemain);
            $bestIndex = array_search(max($finalFitness), $finalFitness);
            $jadwal = $this->convertToSchedule($population[$bestIndex], $ibadahs, $pemain, $periode);

            $generationStats = [
                'generations'     => $this->generations,
                'execution_time'  => round(microtime(true) - $start, 3),
                'best_fitness'    => max($finalFitness),
                'total_ibadah'    => count($ibadahs),
                'total_pemain'    => count($pemain),
            ];

            // Store jadwal in session untuk simpan nanti
            session(['generated_jadwal' => $jadwal]);
        }

        return view('generate_jadwal', compact('periodes','selectedPeriode','jadwal','generationStats','jadwalTersimpan'));
    }

    // METHOD: Simpan jadwal ke database (dipanggil via AJAX)
    public function simpanJadwal(Request $request)
    {
        try {
            $periodeId = $request->periode_id;

            if (!$periodeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periode tidak ditemukan!'
                ]);
            }

            $periode = PeriodeLayanan::find($periodeId);
            if (!$periode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data periode tidak ditemukan!'
                ]);
            }

            // Ambil jadwal dari session
            $jadwalData = session('generated_jadwal');

            if (!$jadwalData || empty($jadwalData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data jadwal tidak ditemukan! Silakan generate ulang.'
                ]);
            }

            // Simpan ke database dalam transaksi
            DB::transaction(function () use ($jadwalData, $periode) {
                // Hapus jadwal lama untuk periode ini (menggunakan nama_periode seperti yang Anda pakai sebelumnya)
                JadwalIbadah::where('periode', $periode->nama_periode)->delete();

                // Simpan jadwal baru
                foreach ($jadwalData as $data) {
                    // pastikan keys sesuai dengan fillable di model JadwalIbadah:
                    // ['periode', 'ibadah_id', 'waktu_ibadah', 'personil', 'alat_musik']
                    JadwalIbadah::create([
                        'periode'      => $periode->nama_periode,
                        'ibadah_id'    => $data['ibadah_id'] ?? null,
                        'waktu_ibadah' => $data['waktu_ibadah'] ?? null,
                        'personil'     => $data['personil'] ?? null,
                        'alat_musik'   => $data['alat'] ?? null,
                    ]);
                }
            });

            // Clear session setelah berhasil simpan
            session()->forget('generated_jadwal');

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil disimpan! Total ' . count($jadwalData) . ' jadwal ibadah telah ditambahkan.',
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan jadwal. ' . $e->getMessage()
            ]);
        }
    }

    // ====== FUNCTION GA SAMA SEPERTI SEBELUMNYA ======
    private function generatePopulation($ibadahs, $pemain)
    {
        $population = [];
        $pemainIds = $pemain->pluck('id')->toArray();

        for ($i = 0; $i < $this->populationSize; $i++) {
            $chromosome = [];
            foreach ($ibadahs as $ibadah) {
                $jumlah = rand($this->minPersonil, min($this->maxPersonil, count($pemainIds)));
                $chromosome[] = collect($pemainIds)->shuffle()->take($jumlah)->toArray();
            }
            $population[] = $chromosome;
        }
        return $population;
    }

    private function evaluatePopulation($population, $pemain)
    {
        $scores = [];
        foreach ($population as $chromosome) {
            $score = 100;
            for ($i = 0; $i < count($chromosome)-1; $i++) {
                $overlap = array_intersect($chromosome[$i], $chromosome[$i+1]);
                $score -= count($overlap)*10;
            }
            $scores[] = max($score,0);
        }
        return $scores;
    }

    private function crossoverPopulation($population, $fitnessScores)
    {
        arsort($fitnessScores);
        $keys = array_keys($fitnessScores);
        $parent1 = $population[$keys[0]];
        $parent2 = $population[$keys[1]];

        $mid = intdiv(count($parent1),2);
        $child1 = array_merge(array_slice($parent1,0,$mid), array_slice($parent2,$mid));
        $child2 = array_merge(array_slice($parent2,0,$mid), array_slice($parent1,$mid));

        $population[$keys[0]] = $child1;
        $population[$keys[1]] = $child2;

        return $population;
    }

    private function mutatePopulation($population, $pemain)
    {
        $pemainIds = $pemain->pluck('id')->toArray();
        foreach ($population as &$chromosome) {
            foreach ($chromosome as &$gen) {
                if (rand(0,100)/100 < 0.2) {
                    if (count($gen) === 0) continue;
                    $index = rand(0,count($gen)-1);
                    $gen[$index] = $pemainIds[array_rand($pemainIds)];
                    $gen = array_values(array_unique($gen));
                }
            }
        }
        return $population;
    }

    // Method untuk convert ke format tampilan (view)
    private function convertToSchedule($chromosome, $ibadahs, $pemain, $periode)
    {
        $jadwal = [];
        $ibadahs = $ibadahs->values();

        foreach ($chromosome as $i => $gen) {
            $ibadah = $ibadahs[$i];
            $selected = $pemain->whereIn('id', $gen);

            $jadwal[] = [
                'nama_ibadah'     => $ibadah->nama_ibadah,
                'waktu_ibadah'    => $ibadah->waktu_ibadah,
                'periode'         => $periode->nama_periode,
                'personil'        => implode(', ', $selected->pluck('nama_pemain')->toArray()),
                'alat'            => implode(', ', $selected->pluck('alat.nama_alat')->toArray()),
                'jumlah_personil' => count($selected),
                'ibadah_id'       => $ibadah->id,
                'pemain_ids'      => $gen
            ];
        }
        return $jadwal;
    }
}
