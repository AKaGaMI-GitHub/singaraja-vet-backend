<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterObat extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function jenis_obat() {
        return $this->belongsTo(MasterJenisObat::class, 'jenis_obat_id', 'id');
    }
}
