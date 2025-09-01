<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeriodeLayanan;
use App\Models\Ibadah;
use App\Models\PemainMusik;
use App\Models\AlatMusik;
use App\Models\AlatPemain;
use App\Models\JadwalIbadah;

class GenerateJadwalController extends Controller
{
    private $populationSize = 8;
    private $generations = 10;
    private $crossoverRate = 0.8;
    private $mutationRate = 0.2;

    public function index(Request $request)
    {
        $periodes = PeriodeLayanan::all();
        $selectedPeriode = $request->get('periode'); // sekarang ini adalah nama_periode
        $jadwal = [];
        $generationStats = null;
        $jadwalTersimpan = JadwalIbadah::all();

        // Ambil data dari session jika ada
        if (session('generated_jadwal')) {
            $jadwal = session('generated_jadwal');
            $generationStats = session('generation_stats');
            $selectedPeriode = $selectedPeriode ?? session('selected_periode');
        }

        return view('generate_jadwal', compact(
            'periodes',
            'selectedPeriode', 
            'jadwal',
            'generationStats',
            'jadwalTersimpan'
        ));
    }

   public function generate(Request $request)
    {
        $periodeNama = $request->get('periode');
        if (!$periodeNama) {
            return redirect()->back()->with('error', 'Periode harus dipilih!');
        }
        // Reset session generate sebelumnya agar histori lama hilang
        session()->forget(['generated_jadwal', 'generation_stats', 'selected_periode', 'last_generated_periode']);

        $periode = PeriodeLayanan::where('nama_periode', $periodeNama)->first();
        if (!$periode) {
            return redirect()->back()->with('error', 'Periode tidak ditemukan!');
        }

        // Filter ibadah berdasarkan nama_periode yang sudah ada di tabel ibadah
        $filteredIbadah = Ibadah::where('nama_periode', $periodeNama)->get();

        if ($filteredIbadah->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data ibadah untuk periode "' . $periodeNama . '".');
        }

        // Cek data pendukung lainnya
        $pemainCount = PemainMusik::count();
        $alatCount = AlatMusik::count();
        $alatPemainCount = AlatPemain::count();

        if ($pemainCount == 0) {
            return redirect()->back()->with('error', 'Tidak ada data pemain musik. Tambahkan pemain terlebih dahulu.');
        }

        if ($alatCount == 0) {
            return redirect()->back()->with('error', 'Tidak ada data alat musik. Tambahkan alat musik terlebih dahulu.');
        }

        if ($alatPemainCount == 0) {
            return redirect()->back()->with('error', 'Tidak ada kombinasi pemain-alat musik. Atur kombinasi pemain dan alat terlebih dahulu.');
        }

        try {
            list($jadwal, $generationStats) = $this->generateWithGeneticAlgorithm($periodeNama, $filteredIbadah);
            
            if (empty($jadwal)) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat generate jadwal. Terjadi kesalahan pada algoritma genetika.');
            }
            
            // Simpan last_generated_periode di session
            session(['last_generated_periode' => $periodeNama]);

            session([
                'generated_jadwal' => $jadwal, 
                'generation_stats' => $generationStats,
                'selected_periode' => $periodeNama
            ]);

            return redirect()->route('generate.jadwal', ['periode' => $periodeNama])
                    ->with('success', 'Jadwal berhasil di-generate untuk periode "' . $periodeNama . '"! Silakan review hasil dan klik "Simpan Jadwal" jika sudah sesuai.');
                    
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat generate jadwal: ' . $e->getMessage());
        }
    }

    public function saveGenerated(Request $request)
    {
        $generatedJadwal = session('generated_jadwal');
        $periodeNama = $request->get('periode') ?? session('selected_periode');
        $periode = PeriodeLayanan::where('nama_periode', $periodeNama)->first();

        if (!$generatedJadwal || !$periode) {
            return redirect()->back()->with('error', 'Tidak ada jadwal yang siap disimpan. Silakan generate jadwal terlebih dahulu.');
        }

        try {
            $savedCount = 0;
            
            // Simpan ke database
            foreach ($generatedJadwal as $ibadahJadwal) {
                foreach ($ibadahJadwal['alat_assignments'] as $assignment) {
                    JadwalIbadah::create([
                        'nama_periode'  => $periodeNama,
                        'nama_ibadah'   => $ibadahJadwal['nama_ibadah'],
                        'waktu_ibadah'  => $ibadahJadwal['waktu_ibadah'],
                        'nama_pemain'   => $assignment['nama_pemain'],
                        'nama_alat'     => $assignment['nama_alat'],
                    ]);
                    $savedCount++;
                }
            }
            
            // Hapus data dari session setelah disimpan
            session()->forget(['generated_jadwal', 'generation_stats', 'selected_periode']);

            return redirect()->back()->with('success', 
                'Jadwal untuk periode "' . $periodeNama . '" berhasil disimpan! Total ' . $savedCount . ' record disimpan.'
            );

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan jadwal: ' . $e->getMessage());
        }
    }

    private function generateWithGeneticAlgorithm($periodeNama, $filteredIbadah = null)
    {
        $startTime = microtime(true);
        
        $periode = PeriodeLayanan::where('nama_periode', $periodeNama)->first();
        if (!$periode) {
            return [[], null];
        }

        // Gunakan ibadah yang sudah difilter jika ada, jika tidak ambil semua
        $ibadahList = $filteredIbadah ?? Ibadah::all();
        $pemainList = PemainMusik::all();
        $alatList = AlatMusik::all();

        if ($ibadahList->isEmpty() || $pemainList->isEmpty() || $alatList->isEmpty()) {
            return [[], null];
        }

        $alatPemainCombinations = AlatPemain::with(['pemain', 'alat'])->get();
        if ($alatPemainCombinations->isEmpty()) {
            return [[], null];
        }

        $population = $this->initializePopulation($ibadahList, $pemainList, $alatList);
        if (empty($population)) {
            return [[], null];
        }

        $bestFitnessHistory = [];

        for ($g = 0; $g < $this->generations; $g++) {
            $fitnessScores = $this->evaluateFitness($population);
            $bestFitnessHistory[] = max($fitnessScores);
            
            $newPopulation = [];

            while (count($newPopulation) < $this->populationSize) {
                $parent1 = $this->selection($population, $fitnessScores);
                $parent2 = $this->selection($population, $fitnessScores);

                if (mt_rand() / mt_getrandmax() < $this->crossoverRate) {
                    $children = $this->crossover($parent1, $parent2);
                } else {
                    $children = [$parent1, $parent2];
                }

                foreach ($children as &$child) {
                    if (mt_rand() / mt_getrandmax() < $this->mutationRate) {
                        $child = $this->mutate($child, $pemainList, $alatList);
                    }
                }

                $newPopulation = array_merge($newPopulation, $children);
            }

            $population = array_slice($newPopulation, 0, $this->populationSize);
        }

        $fitnessScores = $this->evaluateFitness($population);
        $bestIndex = array_search(max($fitnessScores), $fitnessScores);
        $bestSolution = $population[$bestIndex];

        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);

        $generationStats = [
            'generations' => $this->generations,
            'execution_time' => $executionTime,
            'best_fitness' => max($fitnessScores),
            'total_ibadah' => count($ibadahList),
            'total_pemain' => count($pemainList),
            'fitness_history' => $bestFitnessHistory
        ];

        return [$bestSolution, $generationStats];
    }
    private function initializePopulation($ibadahList, $pemainList, $alatList)
    {
        $population = [];

        // Ambil semua alat musik yang tersedia
        $allAlats = $alatList->pluck('nama_alat')->toArray();

        // Ambil mapping pemain ↔ alat musik
        $validCombos = AlatPemain::with(['pemain', 'alat'])
            ->get()
            ->groupBy('alat.nama_alat')
            ->map(function ($items) {
                return $items->map(function ($item) {
                    return $item->pemain->nama_pemain;
                })->toArray();
            })
            ->toArray();

        // Validasi apakah ada kombinasi valid
        if (empty($validCombos)) {
            return [];
        }

        for ($i = 0; $i < $this->populationSize; $i++) {
            $individual = [];

            foreach ($ibadahList as $ibadah) {
                $alatAssignments = [];
                $usedPlayers = [];

                // Assign pemain untuk setiap alat musik
                foreach ($allAlats as $namaAlat) {
                    if (isset($validCombos[$namaAlat]) && !empty($validCombos[$namaAlat])) {
                        // Cari pemain yang bisa memainkan alat ini dan belum digunakan
                        $availablePlayers = array_diff($validCombos[$namaAlat], $usedPlayers);

                        if (!empty($availablePlayers)) {
                            $selectedPlayer = $availablePlayers[array_rand($availablePlayers)];
                            $usedPlayers[] = $selectedPlayer;

                            $alatAssignments[] = [
                                'nama_pemain' => $selectedPlayer,
                                'nama_alat'   => $namaAlat,
                            ];
                        } else {
                            // Jika tidak ada pemain yang tersedia, pilih random dari yang bisa memainkan alat ini
                            $selectedPlayer = $validCombos[$namaAlat][array_rand($validCombos[$namaAlat])];

                            $alatAssignments[] = [
                                'nama_pemain' => $selectedPlayer,
                                'nama_alat'   => $namaAlat,
                            ];
                        }
                    }
                }

                $individual[] = [
                    'nama_ibadah'      => $ibadah->nama_ibadah,
                    'waktu_ibadah'     => $ibadah->waktu_ibadah,
                    'alat_assignments' => $alatAssignments,
                ];
            }

            $population[] = $individual;
        }

        return $population;
    }

    private function evaluateFitness($population)
    {
        $scores = [];

        // Ambil mapping valid combinations
        $validCombos = AlatPemain::with(['pemain', 'alat'])
            ->get()
            ->map(function ($row) {
                return $row->pemain->nama_pemain . '|' . $row->alat->nama_alat;
            })
            ->toArray();

        foreach ($population as $individual) {
            $score = 0;

            foreach ($individual as $ibadahJadwal) {
                $usedPlayersInIbadah = [];

                foreach ($ibadahJadwal['alat_assignments'] as $assignment) {
                    $namaPlayer = $assignment['nama_pemain'];
                    $namaAlat = $assignment['nama_alat'];

                    // Penalti besar untuk duplikasi pemain dalam satu ibadah
                    if (in_array($namaPlayer, $usedPlayersInIbadah)) {
                        $score -= 10; // Penalti berat untuk konflik pemain
                    } else {
                        $score += 3;  // Reward untuk tidak ada konflik
                        $usedPlayersInIbadah[] = $namaPlayer;
                    }

                    // Cek apakah kombinasi pemain-alat valid
                    $key = $namaPlayer . '|' . $namaAlat;

                    if (in_array($key, $validCombos)) {
                        $score += 5; // Reward untuk kombinasi valid
                    } else {
                        $score -= 8; // Penalti untuk kombinasi tidak valid
                    }
                }

                // Bonus jika semua alat terisi tanpa konflik pemain
                if (count($usedPlayersInIbadah) == count($ibadahJadwal['alat_assignments'])) {
                    $score += 10;
                }
            }

            $scores[] = $score;
        }

        return $scores;
    }

    private function selection($population, $fitnessScores)
    {
        // Pastikan tidak ada fitness negatif untuk roulette wheel selection
        $minFitness = min($fitnessScores);
        $adjustedScores = array_map(function ($score) use ($minFitness) {
            return $score - $minFitness + 1;
        }, $fitnessScores);

        $totalFitness = array_sum($adjustedScores);
        if ($totalFitness <= 0) {
            return $population[array_rand($population)];
        }

        $rand = mt_rand() / mt_getrandmax() * $totalFitness;
        $runningSum = 0;

        foreach ($population as $i => $individual) {
            $runningSum += $adjustedScores[$i];
            if ($runningSum >= $rand) {
                return $individual;
            }
        }

        return $population[array_rand($population)];
    }

    private function crossover($parent1, $parent2)
    {
        if (empty($parent1) || empty($parent2)) {
            return [$parent1, $parent2];
        }

        $point = rand(1, count($parent1) - 1);

        $child1 = array_merge(array_slice($parent1, 0, $point), array_slice($parent2, $point));
        $child2 = array_merge(array_slice($parent2, 0, $point), array_slice($parent1, $point));

        return [$child1, $child2];
    }

    private function mutate($individual, $pemainList, $alatList)
    {
        // Ambil mapping pemain per alat untuk mutasi
        $validCombos = AlatPemain::with(['pemain', 'alat'])
            ->get()
            ->groupBy('alat.nama_alat')
            ->map(function ($items) {
                return $items->map(function ($item) {
                    return $item->pemain->nama_pemain;
                })->toArray();
            })
            ->toArray();

        if (empty($individual)) {
            return $individual;
        }

        // Pilih ibadah random untuk dimutasi
        $ibadahIndex = array_rand($individual);
        $ibadahJadwal = &$individual[$ibadahIndex];

        // Pilih assignment random dalam ibadah tersebut
        if (!empty($ibadahJadwal['alat_assignments'])) {
            $assignmentIndex = array_rand($ibadahJadwal['alat_assignments']);
            $assignment = &$ibadahJadwal['alat_assignments'][$assignmentIndex];
            $namaAlat = $assignment['nama_alat'];

            // Ganti dengan pemain random yang bisa memainkan alat ini
            if (isset($validCombos[$namaAlat]) && !empty($validCombos[$namaAlat])) {
                $assignment['nama_pemain'] = $validCombos[$namaAlat][array_rand($validCombos[$namaAlat])];
            }
        }

        return $individual;
    }
}