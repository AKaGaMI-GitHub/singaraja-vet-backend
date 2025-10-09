<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ActivityHelpers;
use App\Http\Helpers\APIHelpers;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function listRoom($id)
    {
        try {
            $room = ChatRoom::with('user')->where('user_id', $id)->get();
            Log::info('Berhasil mengecek chat room!');
            return APIHelpers::responseAPI($room, 200);
        } catch (Exception $error) {
            Log::error('Gagal mengecek chat room! Error : ' . $error->getMessage());
            ActivityHelpers::LogActivityHelpers('Gagal mengecek Chat Room!', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['status_room' => false, 'message' => $error->getMessage()], 500);
        }
    }

    public function newRoom($id)
    {
        try {
            DB::beginTransaction();
            $data = ChatRoom::where('user_id', $id)->latest();
            $uuid = $data->room_id;
            if ($data == null) {
                $newRoom = ChatRoom::create([
                    'room_id' => Str::uuid(),
                    'user_id' => $id
                ]);
                $uuid += $newRoom->room_id;
                Log::info('Berhasil membuat chat room!');
                ActivityHelpers::LogActivityHelpers('Berhasil membuat chat room!', $newRoom, '1');
            }
            $message = ChatMessage::with('user')->where('room_id', $uuid)->first();
            Log::info('Berhasil direction ke chat room!');
            ActivityHelpers::LogActivityHelpers('Berhasil direction chat room!', $message, '1');
            DB::commit();
            return APIHelpers::responseAPI($message, 200);
        } catch (Exception $error) {
            Log::error('Gagal membuat chat room baru!');
            DB::rollBack();
            ActivityHelpers::LogActivityHelpers('Gagal membuat chat room baru!', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }

    public function sendMessage(Request $request, $uuid)
    {
        try {
            DB::beginTransaction();
            $validate = $request->validate([
                'message' => 'required|string|min:1',
                'file' => 'nullable|file|mimes:jpeg,jpg,png|max:5048'
            ]);

            $data = [
                'room_id' => $uuid,
                'user_id' => Auth::guard('sanctum')->id(),
                'message' => $validate['message'],
            ];

            if ($request->file('file')) {
                $urlFile = $validate['file']->store('/message/file_attach', 'public');
                $data['file_attach'] = $urlFile;
            }

            $newMessage = ChatMessage::create($data);


            DB::commit();
        } catch (Exception $error) {
        }
    }

    public function detailRoom($uuid)
    {
        try {
            $data = ChatMessage::with('user')->where('room_id', $uuid)->first();
            if ($data === null) {
                Log::error('Gagal mendapatkan detail room');
                ActivityHelpers::LogActivityHelpers('Gagal mendapatkan detail room chat!', ['message' => 'Room chat tidak ditemukan!'], '0');
                return APIHelpers::responseAPI(['message' => 'Room chat tidak ditemukan!'], 500);
            }
            Log::info('Berhasil mendapatkan detail room');
            return APIHelpers::responseAPI($data, 200);
        } catch (Exception $error) {
            Log::error('Gagal mendapatkan detail room, Error : ' . $error->getMessage());
            ActivityHelpers::LogActivityHelpers('Gagal mendapatkan detail room chat!', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }
}
