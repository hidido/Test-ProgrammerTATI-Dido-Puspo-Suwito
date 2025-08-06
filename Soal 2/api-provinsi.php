<?php

// ========================================================================
// LANGKAH 1: MEMBUAT MODEL & MIGRATION
// ========================================================================
// Jalankan perintah ini di terminal Anda untuk membuat file model dan migration:
// php artisan make:model Provinsi -m
//
// Setelah itu, buka file migration yang baru dibuat di:
// database/migrations/xxxx_xx_xx_xxxxxx_create_provinsis_table.php
// dan modifikasi method `up()` seperti di bawah ini.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvinsisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provinsis', function (Blueprint $table) {
            // ID dari wilayah.id biasanya berupa string (contoh: '11' untuk Aceh)
            // jadi kita gunakan string dan set sebagai primary key.
            $table->string('id')->primary();
            $table->string('name');
            // timestamps() akan membuat kolom created_at dan updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('provinsis');
    }
}

// Setelah memodifikasi file migration, jalankan perintah ini di terminal:
// php artisan migrate


// ========================================================================
// LANGKAH 2: MENGATUR MODEL
// ========================================================================
// Buka file model di: app/Models/Provinsi.php
// Dan sesuaikan isinya seperti ini.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    use HasFactory;

    // Karena kita menggunakan ID string, kita perlu memberitahu Laravel
    // bahwa primary key kita bukan auto-incrementing integer.
    public $incrementing = false;
    protected $keyType = 'string';

    // Tentukan field mana saja yang boleh diisi secara massal (mass assignable).
    protected $fillable = [
        'id',
        'name',
    ];
}


// ========================================================================
// LANGKAH 3: MEMBUAT CONTROLLER
// ========================================================================
// Jalankan perintah ini di terminal untuk membuat API controller:
// php artisan make:controller Api/ProvinsiController --api
//
// Kemudian, buka file controller di: app/Http/Controllers/Api/ProvinsiController.php
// dan isi dengan logika CRUD berikut.

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Provinsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ProvinsiController extends Controller
{
    /**
     * GET /api/provinsi
     * Menampilkan daftar semua provinsi.
     */
    public function index()
    {
        $provinsis = Provinsi::orderBy('name', 'asc')->get();
        return response()->json([
            'success' => true,
            'message' => 'Daftar semua provinsi berhasil diambil.',
            'data'    => $provinsis,
        ], 200);
    }

    /**
     * POST /api/provinsi
     * Menambah provinsi baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'   => 'required|string|max:2|unique:provinsis',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $provinsi = Provinsi::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Provinsi baru berhasil ditambahkan.',
            'data'    => $provinsi,
        ], 201);
    }

    /**
     * GET /api/provinsi/{id}
     * Menampilkan detail satu provinsi.
     */
    public function show($id)
    {
        $provinsi = Provinsi::find($id);

        if ($provinsi) {
            return response()->json([
                'success' => true,
                'message' => 'Detail provinsi ditemukan.',
                'data'    => $provinsi,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Provinsi tidak ditemukan.',
        ], 404);
    }

    /**
     * PUT /api/provinsi/{id}
     * Mengupdate data provinsi tertentu.
     */
    public function update(Request $request, $id)
    {
        $provinsi = Provinsi::find($id);

        if (!$provinsi) {
            return response()->json([
                'success' => false,
                'message' => 'Provinsi tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $provinsi->update($request->only('name'));

        return response()->json([
            'success' => true,
            'message' => 'Provinsi berhasil diperbarui.',
            'data'    => $provinsi,
        ], 200);
    }

    /**
     * DELETE /api/provinsi/{id}
     * Menghapus provinsi tertentu.
     */
    public function destroy($id)
    {
        $provinsi = Provinsi::find($id);

        if (!$provinsi) {
            return response()->json([
                'success' => false,
                'message' => 'Provinsi tidak ditemukan.',
            ], 404);
        }

        $provinsi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Provinsi berhasil dihapus.',
        ], 200);
    }
    
    /**
     * [NILAI TAMBAH]
     * GET /api/provinsi/fetch-from-source
     * Mengambil data dari API eksternal dan menyimpannya ke database.
     */
    public function fetchFromSource()
    {
        // Sumber data JSON dari API publik wilayah Indonesia
        $response = Http::get('https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json');

        if ($response->failed()) {
            return response()->json(['message' => 'Gagal mengambil data dari sumber eksternal.'], 500);
        }

        $provinsisFromApi = $response->json();
        $count = 0;

        foreach ($provinsisFromApi as $data) {
            // updateOrCreate akan memperbarui data jika ID sudah ada, atau membuat baru jika belum ada.
            Provinsi::updateOrCreate(
                ['id' => $data['id']],
                ['name' => $data['name']]
            );
            $count++;
        }

        return response()->json(['message' => "Proses selesai. $count provinsi berhasil disinkronkan."], 200);
    }
}


// ========================================================================
// LANGKAH 4: MENDAFTARKAN ROUTE
// ========================================================================
// Buka file routes/api.php dan tambahkan kode berikut.

use App\Http\Controllers\Api\ProvinsiController;
use Illuminate\Support\Facades\Route;

// Route ini akan secara otomatis membuat semua endpoint yang dibutuhkan untuk CRUD:
// GET    /api/provinsi        -> index()
// POST   /api/provinsi        -> store()
// GET    /api/provinsi/{id}   -> show()
// PUT    /api/provinsi/{id}   -> update()
// DELETE /api/provinsi/{id}   -> destroy()
Route::apiResource('provinsi', ProvinsiController::class);

// Route khusus untuk mengambil data dari sumber eksternal
Route::get('provinsi/fetch-from-source', [ProvinsiController::class, 'fetchFromSource']);

