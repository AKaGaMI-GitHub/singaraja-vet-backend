<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'user_details';

    public function user() {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }
}
