<?php

/**
 * Menampilkan deret bilangan dari 1 sampai $n dengan aturan tertentu.
 * - Kelipatan 4 & 5: "helloworld"
 * - Kelipatan 4: "hello"
 * - Kelipatan 5: "world"
 * - Lainnya: angka itu sendiri
 *
 * @param int $n Batas atas deret bilangan.
 */
function helloworld(int $n)
{
    // Loop dari 1 sampai n
    for ($i = 1; $i <= $n; $i++) {
        // Cek kondisi yang paling spesifik terlebih dahulu (kelipatan 4 DAN 5)
        if ($i % 4 == 0 && $i % 5 == 0) {
            echo "helloworld ";
        } 
        // Kemudian cek kondisi yang kurang spesifik
        elseif ($i % 4 == 0) {
            echo "hello ";
        } 
        elseif ($i % 5 == 0) {
            echo "world ";
        } 
        // Jika tidak ada kondisi yang terpenuhi
        else {
            echo $i . " ";
        }
    }
    // Tambahkan baris baru di akhir untuk kerapian output
    echo "\n";
}

// Contoh penggunaan:
echo "helloworld(6):\n";
helloworld(6);
// Output: 1 2 3 hello world 6

echo "\nhelloworld(10):\n";
helloworld(10);
// Output: 1 2 3 hello world 6 7 hello 9 world 

echo "\nhelloworld(20):\n";
helloworld(20);
// Output: 1 2 3 hello world 6 7 hello 9 world 11 hello 13 14 world 16 17 18 19 helloworld

?>
