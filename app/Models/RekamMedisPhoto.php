<?php

namespace App\Models;

use App\Http\Helpers\ImageHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedisPhoto extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = ['photos_url'];

    public function getPhotosUrlAttribute()
    {
        return ImageHelpers::ImageCheckerHelpers($this->photos);
    }

    public function hewan()
    {
        return $this->belongsTo(RekamMedis::class, 'rekam_medis_id', 'id');
    }
}
