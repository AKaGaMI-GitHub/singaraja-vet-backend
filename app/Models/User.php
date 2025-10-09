<?php

namespace App\Models;

use App\Http\Helpers\ImageHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $appends = ['avatar_photos'];
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id',
        'password',
        'remember_token',
        // 'is_vet',
        'updated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getAvatarPhotosAttribute()
    {
        return ImageHelpers::ImageCheckerHelpers($this->avatar);
    }

    public function user_detail()
    {
        return $this->belongsTo(UserDetail::class, 'id', 'user_id');
    }

    public function pets()
    {
        return $this->hasMany(Pets::class, 'id', 'user_id');
    }

    public function rekam_medis()
    {
        return $this->hasMany(RekamMedis::class, 'id', 'user_id');
    }

    public function chat_room()
    {
        return $this->hasOne(ChatRoom::class, 'id', 'user_id');
    }
}
