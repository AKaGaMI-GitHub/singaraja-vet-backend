<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function komentar() {
        return $this->hasMany(BlogComment::class, 'blog_id', 'id');
    }
}
