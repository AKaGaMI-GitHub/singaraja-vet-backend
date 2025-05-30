<?php

namespace App\Http\Controllers\Api\Admin\MasterData\MasterHewan;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\Master\MasterRasHewan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MasterRasHewanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = MasterRasHewan::query();

            if ($request->has('keyword')) {
                $data = $data->where('nama_ras_hewan', 'like', '%' . $request->keyword . '%');
            }

            if ($request->has('jenis')) {
                $data = $data->where('jenis_hewan_id', $request->jenis);
            }

            if ($request->has('status')) {
                $data = $data->where('is_active', $request->status);
            }

            $data = $data->paginate(8);
            Log::info('Berhasil mendapatkan data Ras Hewan (Admin)');
            return APIHelpers::responseAPI([
                'data' => $data
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal mendapatkan data Ras Hewan (Admin)');
            ActivityHelpers::LogActivityHelpers('Gagal mendapatkan data Ras Hewan (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI([
                'error' => $error->getMessage()
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
                'id_jenis_hewan' => 'required|numeric',
                'nama_ras_hewan' => 'required',
                'is_active' => 'required|in:0,1'
            ]);

            $data = [
                'id_jenis_hewan' => $validate['id_jenis_hewan'],
                'nama_ras_hewan' => $validate['nama_ras_hewan'],
                'is_active' => (string) $validate['is_active']
            ];

            MasterRasHewan::create($data);

            Log::error('Berhasil store data Ras Hewan (Admin)');
            ActivityHelpers::LogActivityHelpers('Berhasil store data Ras Hewan (Admin)', ['data' => $data], '1');
            return APIHelpers::responseAPI([
                'data' => $data
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal store data Ras Hewan (Admin)');
            ActivityHelpers::LogActivityHelpers('Gagal store data Ras Hewan (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI([
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function showList($jenis_hewan_id)
    {
        try {
            $data = MasterRasHewan::where('is_active', '1')->where('jenis_hewan_id', $jenis_hewan_id)->get()->map(function ($item) {
                return [
                    'label' => $item->nama_ras_hewan,
                    'value' => $item->id,
                ];
            });

            Log::error('Berhasil get data Jenis Ras Hewan');
            return APIHelpers::responseAPI([
                'data' => $data
            ], 200);

        } catch (Exception $error) {
            Log::error('Gagal get data Jenis Ras Hewan');
            ActivityHelpers::LogActivityHelpers('Gagal get data Jenis Ras Hewan', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI([
                'error' => $error->getMessage()
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
                'id_jenis_hewan' => 'required|numeric',
                'nama_ras_hewan' => 'required',
                'is_active' => 'required|in:0,1'
            ]);

             $data = [
                'id_jenis_hewan' => $validate['id_jenis_hewan'],
                'nama_ras_hewan' => $validate['nama_ras_hewan'],
                'is_active' => (string) $validate['is_active']
            ];

            MasterRasHewan::findOrFail($id)->update($data);

            Log::error('Berhasil store data Ras Hewan (Admin)');
            ActivityHelpers::LogActivityHelpers('Berhasil store data Ras Hewan (Admin)', ['data' => $data], '1');
            return APIHelpers::responseAPI([
                'data' => $data
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal store data Ras Hewan (Admin)');
            ActivityHelpers::LogActivityHelpers('Gagal store data Ras Hewan (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI([
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function status(string $id)
    {
        try {
            $data = MasterRasHewan::findOrFail($id);
            $data->update(['is_active' => $data->is_active == 1 ? '0' : '1']);

            Log::error('Berhasil merubah status data Jenis Ras Hewan (Admin)');
            ActivityHelpers::LogActivityHelpers('Berhasil merubah status data Jenis Ras Hewan (Admin)', ['data' => $data], '1');
            return APIHelpers::responseAPI([
                'data' => $data
            ], 200);

        } catch (Exception $error) {
            Log::error('Gagal merubah status data Jenis Ras Hewan (Admin)');
            ActivityHelpers::LogActivityHelpers('Gagal merubah status data Jenis Ras Hewan (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI([
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
