<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterJenisObat extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function obat() {
        return $this->hasMany(MasterObat::class, 'id', 'jenis_obat_id');
    }
}
