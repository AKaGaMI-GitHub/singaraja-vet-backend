<?php

namespace App\Http\Controllers\Api\Users;

use App\Events\ChatRoomList;
use App\Events\NewMessage;
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
    public function index()
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if ($user->is_vet == 1) {
                return Self::listRoom();
            } else {
                return Self::newRoom($user->id);
            }
        } catch (Exception $error) {
            Log::error('Gagal mengakses chat! Error : ' . $error->getMessage());
            ActivityHelpers::LogActivityHelpers('Gagal mengakses chat!', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['status_room' => false, 'message' => $error->getMessage()], 500);
        }
    }

    private function listRoom()
    {
        try {
            $room = ChatRoom::getListRoomChat();
            Log::info('Berhasil mengecek chat room!');
            return APIHelpers::responseAPI($room, 200);
        } catch (Exception $error) {
            Log::error('Gagal mengecek chat room! Error : ' . $error->getMessage());
            ActivityHelpers::LogActivityHelpers('Gagal mengecek Chat Room!', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['status_room' => false, 'message' => $error->getMessage()], 500);
        }
    }

    private function newRoom($id)
    {
        try {
            DB::beginTransaction();
            $existingRoom = ChatRoom::where('user_id', $id)->latest()->first();

            $uuid = null;

            if ($existingRoom == null) {
                $newRoom = ChatRoom::create([
                    'room_id' => Str::uuid(),
                    'user_id' => $id
                ]);
                $uuid = $newRoom->room_id;
                Log::info('Berhasil membuat chat room!');
                ActivityHelpers::LogActivityHelpers('Berhasil membuat chat room!', $newRoom, '1');
            } else {
                $uuid = $existingRoom->room_id;
            }

            DB::commit();
            Log::info('Berhasil direction ke chat room!');
            ActivityHelpers::LogActivityHelpers('Berhasil direction chat room!', ['room_id' => $uuid], '1');
            return APIHelpers::responseAPI(['room_id' => $uuid], 200);
        } catch (Exception $error) {
            Log::error('Gagal membuat chat room baru!');
            ActivityHelpers::LogActivityHelpers('Gagal membuat chat room baru!', ['message' => $error->getMessage()], '0');
            DB::rollBack();
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }

    public function sendMessage(Request $request, $uuid)
    {
        try {
            DB::beginTransaction();
            $validate = $request->validate([
                'message' => 'required|string|min:1',
                'file' => 'nullable|file|mimes:jpeg,jpg,png|max:20480'
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
            broadcast(new NewMessage($newMessage))->toOthers();
            broadcast(new ChatRoomList());

            Log::info('Berhasil send new message!');
            ActivityHelpers::LogActivityHelpers('Berhasil send new message!', $newMessage, '1');
            DB::commit();
            return APIHelpers::responseAPI($newMessage, 200);
        } catch (Exception $error) {
            DB::rollBack();
            Log::info('Gagal send new message!');
            ActivityHelpers::LogActivityHelpers('Gagal send new message!', ['message' => $error->getMessage()], '1');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }

    public function detailRoom($uuid)
    {
        try {
            $data = ChatMessage::with('user')->where('room_id', $uuid)->get();
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

    public function ownerRoom($uuid)
    {
        try {
            $data = ChatRoom::with('user')->where('room_id', $uuid)->first();

            if ($data === null) {
                Log::error('Gagal mendapatkan owner room');
                ActivityHelpers::LogActivityHelpers('Gagal mendapatkan owner room chat!', ['message' => 'Room chat tidak ditemukan!'], '0');
                return APIHelpers::responseAPI(['message' => 'Room chat tidak ditemukan!'], 500);
            }
            Log::info('Berhasil mendapatkan owner room');
            return APIHelpers::responseAPI($data, 200);
        } catch (Exception $error) {
            Log::error('Gagal mendapatkan owner room, Error : ' . $error->getMessage());
            ActivityHelpers::LogActivityHelpers('Gagal mendapatkan owner room chat!', ['message' => $error->getMessage()], '0');
            return APIHelpers::responseAPI(['message' => $error->getMessage()], 500);
        }
    }
}
