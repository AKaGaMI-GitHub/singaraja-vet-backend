<?php

namespace App\Http\Controllers\Api\Admin\MasterData\MasterObat;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\Master\MasterObat;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MasterObatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = MasterObat::with('jenis_obat');

            if ($request->has('keyword')) {
                $data = $data->where('nama_obat', 'like', '%' . $request->keyword . '%');
            }

            if ($request->has('jenis_obat')) {
                $data = $data->where('jenis_obat_id', $request->jenis_obat);
            }

            if ($request->has('status')) {
                $data = $data->where('is_active', $request->status);
            }

            $data = $data->paginate(8);
            Log::info('Berhasil mendapatkan data Obat (Admin)');
            return APIHelpers::responseAPI([
                'data' => $data
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal mendapatkan data Obat (Admin)');
            ActivityHelpers::LogActivityHelpers('Gagal mendapatkan data Obat (Admin)', ['message' => $error->getMessage()], '0');
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
            $validate = $request->validate([
                'jenis_obat_id' => 'required|numeric',
                'nama_obat' => 'required',
                'deskripsi_obat' => 'required|max:300',
                'is_active' => 'required|in:0,1'
            ]);

            $data = [
                'jenis_obat_id' => $validate['jenis_obat_id'],
                'nama_obat' => $validate['nama_obat'],
                'deskripsi_obat' => $validate['deskripsi_obat'],
                'is_active' => (string) $validate['is_active']
            ];

            MasterObat::create($data);

            Log::error('Berhasil store data Obat (Admin)');
            ActivityHelpers::LogActivityHelpers('Berhasil store data Obat (Admin)', ['data' => $data], '1');
            return APIHelpers::responseAPI([
                'data' => $data
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal store data Obat (Admin)');
            ActivityHelpers::LogActivityHelpers('Gagal store data Obat (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI([
                'message' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function showList($jenis_obat_id)
    {
        try {
            $data = MasterObat::where('is_active', '1')->where('jenis_obat_id', $jenis_obat_id)->get()->map(function ($item) {
                return [
                    'label' => $item->nama_obat,
                    'value' => $item->id,
                ];
            });

            Log::error('Berhasil get data Jenis Obat');
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
    public function update(Request $request, string $id)
    {
        try {
            $validate = $request->validate([
                'jenis_obat_id' => 'required|numeric',
                'nama_obat' => 'required',
                'deskripsi_obat' => 'required|max:300',
                'is_active' => 'required|in:0,1'
            ]);

             $data = [
                'jenis_obat_id' => $validate['jenis_obat_id'],
                'nama_obat' => $validate['nama_obat'],
                'deskripsi_obat' => $validate['deskripsi_obat'],
                'is_active' => (string) $validate['is_active']
            ];

            MasterObat::findOrFail($id)->update($data);

            Log::error('Berhasil store data Obat (Admin)');
            ActivityHelpers::LogActivityHelpers('Berhasil store data Obat (Admin)', ['data' => $data], '1');
            return APIHelpers::responseAPI([
                'data' => $data
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal store data Obat (Admin)');
            ActivityHelpers::LogActivityHelpers('Gagal store data Obat (Admin)', ['message' => $error->getMessage()], '0');
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
            $data = MasterObat::findOrFail($id);
            $data->update(['is_active' => $data->is_active == 1 ? '0' : '1']);

            Log::error('Berhasil merubah status data Jenis Obat (Admin)');
            ActivityHelpers::LogActivityHelpers('Berhasil merubah status data Jenis Obat (Admin)', ['data' => $data], '1');
            return APIHelpers::responseAPI([
                'data' => $data
            ], 200);

        } catch (Exception $error) {
            Log::error('Gagal merubah status data Jenis Obat (Admin)');
            ActivityHelpers::LogActivityHelpers('Gagal merubah status data Jenis Obat (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI([
                'message' => $error->getMessage()
            ], 500);
        }
    }
}
