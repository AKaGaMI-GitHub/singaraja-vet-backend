<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogComment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function blog()
    {
        return $this->belongsTo(Blog::class, 'id', 'blog_id');
    }

    public function balasan()
    {
        return $this->hasMany(BlogComment::class, 'parent_comment_id')->with('balasan');
    }

    public function parent()
    {
        return $this->belongsTo(BlogComment::class, 'parent_comment_id');
    }
}
