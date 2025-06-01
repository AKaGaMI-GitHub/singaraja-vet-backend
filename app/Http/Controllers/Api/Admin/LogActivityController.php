<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\LogActivity;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogActivityController extends Controller
{
    public function index(Request $request) {
        try {
            $data = LogActivity::with('user');

            if ($request->has('keyword')) {
                $data = $data->where(function ($query) use ($request) {
                    $query->where('activity', 'like', '%' . $request->keyword . '%')
                        ->orWhereHas('user', function ($q) use ($request) {
                            $q->where('username', 'like', '%' . $request->keyword . '%')
                                ->orWhere('nama_depan', 'like', '%' . $request->keyword . '%')
                                ->orWhere('nama_belakang', 'like', '%' . $request->keyword . '%');
                        });
                });
            }

            if ($request->has('status')) {
                $data = $data->where('status', $request->status);
            }

            $data = $data->orderBy('id', 'DESC')->paginate(8);
            Log::info('Berhasil Mendapatkan data Log Activity! (Admin)');
            return APIHelpers::responseAPI($data, 200);
        } catch (Exception $error) {
            Log::error('Gagal Mendapatkan data Log Activity! (Admin)');
            ActivityHelpers::LogActivityHelpers('Gagal Mendapatkan data Log Activity! (Admin)', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI([
                'message' => $error->getMessage()
            ], 500);
        }
    }
}
