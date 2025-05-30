<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterRasHewan extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function jenis_hewan() {
        return $this->belongsTo(MasterJenisHewan:: class, 'jenis_hewan_id', 'id');
    }
}
