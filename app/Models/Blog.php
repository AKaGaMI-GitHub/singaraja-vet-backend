<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Blog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['id', 'user_id', 'thumbnail', 'tags', 'updated_at'];

    protected $appends = ['thumbnail_url', 'tags_parsed'];

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? url(Storage::url($this->getRawOriginal('thumbnail'))) : null;
    }

    public function getTagsParsedAttribute()
    {  
        return json_decode($this->getRawOriginal('tags'));
    }

    public function author() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function komentar() {
        return $this->hasMany(BlogComment::class, 'blog_id', 'id');
    }
}
