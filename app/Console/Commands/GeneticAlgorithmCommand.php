<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GeneticAlgorithmCommand extends Command
{
    protected $signature = 'ga:demo';
    protected $description = 'Demo Algoritma Genetika untuk penjadwalan ibadah dengan encoding contoh';

    private $populationSize = 3;
    private $generations = 3;
    private $crossoverRate = 0.8;
    private $mutationRate = 0.3;

    public function handle()
    {
        $this->info("🧬 DEMO ALGORITMA GENETIKA - PENJADWALAN IBADAH");
        $this->newLine();

        // ===== 1. Tahap Encoding =====
        $this->info("1️⃣ TAHAP ENCODING");
        $pemain = ['A','B','C','D'];
        $alat = ['1','2','3','4'];
        $ibadah = ['I1','I2','I3'];

        $this->info("Pemain: " . implode(',', $pemain));
        $this->info("Alat Musik: " . implode(',', $alat));
        $this->info("Ibadah: " . implode(',', $ibadah));
        $this->newLine();

        // Encode menjadi kromosom: setiap ibadah diassign pemain & alat
        $this->info("📋 Contoh Encoding (Gen per Ibadah):");
        foreach ($ibadah as $i => $ib) {
            $this->info("  {$ib}: ");
            foreach ($pemain as $p) {
                $alatRandom = $alat[array_rand($alat)];
                $this->info("     • {$p} -> {$alatRandom}");
            }
        }
        $this->newLine();

        // ===== 2. Tahap Generate Populasi =====
        $this->info("2️⃣ TAHAP GENERATE POPULASI");
        $populasi = $this->generatePopulation($pemain, $alat, $ibadah, $this->populationSize);
        foreach ($populasi as $i => $ind) {
            $this->info("Individu " . ($i+1) . ": " . json_encode($ind));
        }
        $this->newLine();

        // ===== 3. Tahap Evaluasi Fitness =====
        $this->info("3️⃣ TAHAP EVALUASI FITNESS");
        $fitnessScores = $this->evaluatePopulation($populasi);
        foreach ($fitnessScores as $i => $f) {
            $this->info("Individu " . ($i+1) . " Fitness: {$f}%");
        }
        $this->newLine();

        // ===== 4. Tahap CrossOver =====
        $this->info("4️⃣ TAHAP CROSSOVER");
        $parent1 = $populasi[0];
        $parent2 = $populasi[1];
        $this->info("Parent 1: " . json_encode($parent1));
        $this->info("Parent 2: " . json_encode($parent2));

        $children = $this->crossover($parent1, $parent2);
        $this->info("Child 1: " . json_encode($children[0]));
        $this->info("Child 2: " . json_encode($children[1]));
        $this->newLine();

        // ===== 5. Tahap Mutasi =====
        $this->info("5️⃣ TAHAP MUTASI");
        $mutatedChildren = array_map(fn($ind) => $this->mutate($ind, $alat), $children);
        $this->info("Mutated Child 1: " . json_encode($mutatedChildren[0]));
        $this->info("Mutated Child 2: " . json_encode($mutatedChildren[1]));
        $this->newLine();

        $this->info("✅ Demo selesai. Semua tahapan GA telah ditampilkan.");
    }

    private function generatePopulation($pemain, $alat, $ibadah, $size)
    {
        $populasi = [];
        for ($i=0; $i<$size; $i++) {
            $individu = [];
            foreach ($ibadah as $ib) {
                $assignments = [];
                foreach ($pemain as $p) {
                    $alatRandom = $alat[array_rand($alat)];
                    $assignments[$p] = $alatRandom;
                }
                $individu[$ib] = $assignments;
            }
            $populasi[] = $individu;
        }
        return $populasi;
    }

    private function evaluatePopulation($populasi)
    {
        $fitnessScores = [];
        foreach ($populasi as $ind) {
            $validCount = 0;
            foreach ($ind as $ibadah => $assignments) {
                $usedAlat = [];
                foreach ($assignments as $pemain => $alat) {
                    // simple rule: alat tidak boleh dipakai 2x dalam satu ibadah
                    if (!in_array($alat, $usedAlat)) {
                        $validCount++;
                        $usedAlat[] = $alat;
                    }
                }
            }
            $fitnessScores[] = round(($validCount / (count($populasi[0]) * count(array_keys($populasi[0][$ibadah])))) * 100,1);
        }
        return $fitnessScores;
    }

    private function crossover($parent1, $parent2)
    {
        $cut = rand(1, count($parent1)-1);
        $keys = array_keys($parent1);
        $child1 = [];
        $child2 = [];
        foreach ($keys as $i => $key) {
            if ($i < $cut) {
                $child1[$key] = $parent1[$key];
                $child2[$key] = $parent2[$key];
            } else {
                $child1[$key] = $parent2[$key];
                $child2[$key] = $parent1[$key];
            }
        }
        return [$child1, $child2];
    }

    private function mutate($individu, $alat)
    {
        if (rand(0,100)/100 < $this->mutationRate) {
            $ibadahKeys = array_keys($individu);
            $ibRandom = $ibadahKeys[array_rand($ibadahKeys)];
            $pemainKeys = array_keys($individu[$ibRandom]);
            $pRandom = $pemainKeys[array_rand($pemainKeys)];
            $individu[$ibRandom][$pRandom] = $alat[array_rand($alat)];
        }
        return $individu;
    }

}
