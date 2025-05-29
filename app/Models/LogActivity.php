<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogActivity extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'ip_detail' => 'json',
        'detail' => 'json'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
