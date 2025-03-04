<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedis extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function pemilik_hewan()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function hewan()
    {
        return $this->belongsTo(Pets::class, 'pet_id', 'id');
    }

    public function dokumentasi()
    {
        return $this->hasMany(RekamMedisPhoto::class, 'id', 'rekam_medis_id');
    }
}
