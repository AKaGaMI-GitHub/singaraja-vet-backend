<?php

namespace App\Models;

use App\Http\Helpers\ImageHelpers;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    //
    protected $appends = ['file_attach'];

    public function getFileUrlAttribute()
    {
        return ImageHelpers::ImageCheckerHelpers($this->file_attach);
    }

    public function room()
    {
        return $this->belongsTo(ChatRoom::class, 'room_id', 'room_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
