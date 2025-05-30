<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterJenisHewan extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function ras() {
        return $this->hasMany(MasterRasHewan::class, 'id', 'jenis_hewan_id');
    }
}
