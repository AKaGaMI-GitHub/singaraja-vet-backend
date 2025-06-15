<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedis extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['obat_parsed'];

    public function getObatParsedAttribute()
    {  
        return json_decode($this->getRawOriginal('obat_parsed'));
    }

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
        return $this->hasMany(RekamMedisPhoto::class, 'rekam_medis_id', 'id');
    }
}
