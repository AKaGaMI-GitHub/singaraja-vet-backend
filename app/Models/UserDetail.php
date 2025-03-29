<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $hidden = [
        'id',
        'user_id',
        'created_at',
        'updated_at',
        'social_media'
    ];

    protected $appends = ['social_media_list'];
    protected $guarded = [];
    protected $table = 'user_details';

    public function getSocialMediaListAttribute() {
        return json_decode($this->social_media, true);
    }

    public function user() {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }
}
