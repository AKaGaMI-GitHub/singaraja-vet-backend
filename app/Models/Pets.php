<?php

namespace App\Models;

use App\Models\Master\MasterJenisHewan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pets extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function jenis_hewan()
    {
        return $this->hasOne(MasterJenisHewan::class, 'id', 'jenis_hewan_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function detail_photo()
    {
        return $this->hasMany(PetsPhoto::class, 'pet_id', 'id');
    }

    public function rekam_medis()
    {
        return $this->hasMany(RekamMedis::class, 'id', 'pet_id');
    }
}
