<?php

namespace App\Http\Controllers\Api\Admin\MasterData\MasterHewan;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\Master\MasterJenisKelaminHewan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasterJenisKelaminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = MasterJenisKelaminHewan::query();

            if ($request->has('keyword')) {
                $data = $data->where('jenis_kelamin', 'like', '%' . $request->keyword . '%');
            }

            if ($request->has('status')) {
                $data = $data->where('is_active', $request->status);
            }

            $data = $data->paginate(8);
            Log::info('Berhasil mendapatkan data Jenis Kelamin Hewan (Admin)');
            return APIHelpers::responseAPI([
                'data' => $data
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal mendapatkan data Jenis Kelamin Hewan (Admin)');
            ActivityHelpers::LogActivityHelpers('Gagal mendapatkan data Jenis Kelamin Hewan (Admin)', ['message' => $error->getMessage()], '0');
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
                'jenis_kelamin' => 'required',
                'is_active' => 'required|in:0,1'
            ]);

            $data = [
                'jenis_kelamin' => $validate['jenis_kelamin'],
                'is_active' => (string) $validate['is_active']
            ];

            MasterJenisKelaminHewan::create($data);

            Log::info('Berhasil store data Jenis Kelamin Hewan (Admin)');
            ActivityHelpers::LogActivityHelpers('Berhasil store data Jenis Kelamin Hewan (Admin)', ['data' => $data], '1');
            DB::commit();
            return APIHelpers::responseAPI([
                'data' => $data
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal store data Jenis Kelamin Hewan (Admin)');
            DB::rollBack();
            ActivityHelpers::LogActivityHelpers('Gagal store data Jenis Kelamin Hewan (Admin)', ['message' => $error->getMessage()], '0');
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
            $data = MasterJenisKelaminHewan::where('is_active', '1')->get()->map(function ($item) {
                return [
                    'label' => $item->jenis_kelamin,
                    'value' => $item->id,
                ];
            });

            Log::info('Berhasil get data Jenis Kelamin Hewan');
            return APIHelpers::responseAPI([
                'data' => $data
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal get data Jenis Kelamin Hewan');
            ActivityHelpers::LogActivityHelpers('Gagal get data Jenis Kelamin Hewan', ['message' => $error->getMessage()], '0');
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
                'jenis_kelamin' => 'required',
                'is_active' => 'required|in:0,1'
            ]);

            $data = [
                'jenis_kelamin' => $validate['jenis_kelamin'],
                'is_active' => (string) $validate['is_active']
            ];

            MasterJenisKelaminHewan::findOrFail($id)->update($data);
            Log::info('Berhasil update data Jenis Kelamin Hewan (Admin)');
            ActivityHelpers::LogActivityHelpers('Berhasil update data Jenis Kelamin Hewan (Admin)', ['data' => $data], '1');
            DB::commit();
            return APIHelpers::responseAPI([
                'data' => $data
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal update data Jenis Kelamin Hewan (Admin)');
            DB::rollBack();
            ActivityHelpers::LogActivityHelpers('Gagal update data Jenis Kelamin Hewan (Admin)', ['message' => $error->getMessage()], '0');
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
            $data = MasterJenisKelaminHewan::findOrFail($id);
            $data->update(['is_active' => $data->is_active == 1 ? '0' : '1']);

            Log::info('Berhasil merubah status data Jenis Kelamin Hewan (Admin)');
            ActivityHelpers::LogActivityHelpers('Berhasil merubah status data Jenis Kelamin Hewan (Admin)', ['data' => $data], '1');
            DB::commit();
            return APIHelpers::responseAPI([
                'data' => $data
            ], 200);
        } catch (Exception $error) {
            Log::error('Gagal merubah status data Jenis Kelamin Hewan (Admin)');
            DB::rollBack();
            ActivityHelpers::LogActivityHelpers('Gagal merubah status data Jenis Kelamin Hewan (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI([
                'message' => $error->getMessage()
            ], 500);
        }
    }
}
