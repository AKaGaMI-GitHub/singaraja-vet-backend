<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedisPhoto extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function hewan()
    {
        return $this->belongsTo(RekamMedis::class, 'rekam_medis_id', 'id');
    }
}
