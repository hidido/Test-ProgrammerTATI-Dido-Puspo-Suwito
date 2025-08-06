<?php

/**
 * Menentukan predikat kinerja pegawai berdasarkan hasil kerja dan perilaku.
 *
 * @param string $hasil_kerja Nilai bisa: 'dibawah ekspektasi', 'sesuai ekspektasi', 'diatas ekspektasi'
 * @param string $perilaku      Nilai bisa: 'dibawah ekspektasi', 'sesuai ekspektasi', 'diatas ekspektasi'
 * @return string Predikat kinerja.
 */
function predikat_kinerja(string $hasil_kerja, string $perilaku): string
{
    // Normalisasi input ke huruf kecil untuk konsistensi
    $hasil_kerja = strtolower($hasil_kerja);
    $perilaku = strtolower($perilaku);

    if ($hasil_kerja === 'diatas ekspektasi') {
        if ($perilaku === 'diatas ekspektasi') {
            return 'Sangat Baik';
        } elseif ($perilaku === 'sesuai ekspektasi') {
            return 'Baik';
        } elseif ($perilaku === 'dibawah ekspektasi') {
            return 'Kurang/misconduct';
        }
    } elseif ($hasil_kerja === 'sesuai ekspektasi') {
        if ($perilaku === 'diatas ekspektasi') {
            return 'Baik';
        } elseif ($perilaku === 'sesuai ekspektasi') {
            return 'Baik';
        } elseif ($perilaku === 'dibawah ekspektasi') {
            return 'Kurang/misconduct';
        }
    } elseif ($hasil_kerja === 'dibawah ekspektasi') {
        if ($perilaku === 'diatas ekspektasi') {
            return 'Butuh perbaikan';
        } elseif ($perilaku === 'sesuai ekspektasi') {
            return 'Butuh perbaikan';
        } elseif ($perilaku === 'dibawah ekspektasi') {
            return 'Sangat Kurang';
        }
    }

    return 'Kombinasi input tidak valid';
}

// Contoh penggunaan:
$hasil_kerja_1 = 'diatas ekspektasi';
$perilaku_1 = 'diatas ekspektasi';
echo "Hasil: '$hasil_kerja_1', Perilaku: '$perilaku_1' => Predikat: " . predikat_kinerja($hasil_kerja_1, $perilaku_1) . "\n";
// Output: Hasil: 'diatas ekspektasi', Perilaku: 'diatas ekspektasi' => Predikat: Sangat Baik

$hasil_kerja_2 = 'sesuai ekspektasi';
$perilaku_2 = 'dibawah ekspektasi';
echo "Hasil: '$hasil_kerja_2', Perilaku: '$perilaku_2' => Predikat: " . predikat_kinerja($hasil_kerja_2, $perilaku_2) . "\n";
// Output: Hasil: 'sesuai ekspektasi', Perilaku: 'dibawah ekspektasi' => Predikat: Kurang/misconduct

$hasil_kerja_3 = 'dibawah ekspektasi';
$perilaku_3 = 'sesuai ekspektasi';
echo "Hasil: '$hasil_kerja_3', Perilaku: '$perilaku_3' => Predikat: " . predikat_kinerja($hasil_kerja_3, $perilaku_3) . "\n";
// Output: Hasil: 'dibawah ekspektasi', Perilaku: 'sesuai ekspektasi' => Predikat: Butuh perbaikan

?>
