<?php

namespace App\Http\Controllers\Api\Admin\MasterData\MasterObat;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\Master\MasterJenisObat;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasterJenisObatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = MasterJenisObat::query();

            if ($request->has('keyword')) {
                $data = $data->where('nama_jenis_obat', 'like', '%' . $request->keyword . '%');
            }

            if ($request->has('status')) {
                $data = $data->where('is_active', $request->status);
            }

            $data = $data->paginate(8);
            Log::info('Berhasil mendapatkan data Jenis Obat (Admin)');
            return APIHelpers::responseAPI([
                'data' => $data
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal mendapatkan data Jenis Obat (Admin)');
            ActivityHelpers::LogActivityHelpers('Gagal mendapatkan data Jenis Obat (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI([
                'message' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validate = $request->validate([
                'nama_jenis_obat' => 'required',
                'deskripsi_jenis_obat' => 'required|max:300',
                'is_active' => 'required|in:0,1'
            ]);

            $data = [
                'nama_jenis_obat' => $validate['nama_jenis_obat'],
                'deskripsi_jenis_obat' => $validate['deskripsi_jenis_obat'],
                'is_active' => (string) $validate['is_active']
            ];

            MasterJenisObat::create($data);

            Log::info('Berhasil store data Jenis Obat (Admin)');
            ActivityHelpers::LogActivityHelpers('Berhasil store data Jenis Obat (Admin)', ['data' => $data], '1');
            DB::commit();
            return APIHelpers::responseAPI([
                'data' => $data
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal store data Jenis Obat (Admin)');
            DB::rollBack();
            ActivityHelpers::LogActivityHelpers('Gagal store data Jenis Obat (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI([
                'message' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function showList()
    {
        try {
            $data = MasterJenisObat::where('is_active', '1')->get()->map(function ($item) {
                return [
                    'label' => $item->nama_jenis_obat,
                    'value' => $item->id,
                ];
            });

            Log::info('Berhasil get data Jenis Obat');
            return APIHelpers::responseAPI([
                'data' => $data
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal get data Jenis Obat');
            ActivityHelpers::LogActivityHelpers('Gagal get data Jenis Obat', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI([
                'message' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(string $id, Request $request)
    {
        try {
            DB::beginTransaction();
            $validate = $request->validate([
                'nama_jenis_obat' => 'required',
                'deskripsi_jenis_obat' => 'required|max:300',
                'is_active' => 'required|in:0,1'
            ]);

            $data = [
                'nama_jenis_obat' => $validate['nama_jenis_obat'],
                'deskripsi_jenis_obat' => $validate['deskripsi_jenis_obat'],
                'is_active' => (string) $validate['is_active']
            ];

            MasterJenisObat::findOrFail($id)->update($data);

            Log::info('Berhasil update data Jenis Obat (Admin)');
            ActivityHelpers::LogActivityHelpers('Berhasil update data Jenis Obat (Admin)', ['data' => $data], '1');
            DB::commit();
            return APIHelpers::responseAPI([
                'data' => $data
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal update data Jenis Obat (Admin)');
            DB::rollBack();
            ActivityHelpers::LogActivityHelpers('Gagal update data Jenis Obat (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI([
                'message' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function status(string $id)
    {
        try {
            DB::beginTransaction();
            $data = MasterJenisObat::findOrFail($id);
            $data->update(['is_active' => $data->is_active == 1 ? '0' : '1']);

            Log::info('Berhasil merubah status data Jenis Obat (Admin)');
            ActivityHelpers::LogActivityHelpers('Berhasil merubah status data Jenis Obat (Admin)', ['data' => $data], '1');
            DB::commit();
            return APIHelpers::responseAPI([
                'data' => $data
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal merubah status data Jenis Obat (Admin)');
            DB::rollBack();
            ActivityHelpers::LogActivityHelpers('Gagal merubah status data Jenis Obat (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI([
                'message' => $error->getMessage()
            ], 500);
        }
    }
}
