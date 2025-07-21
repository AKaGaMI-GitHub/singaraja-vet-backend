<?php

namespace App\Models;

use App\Http\Helpers\ImageHelpers;
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
        return ImageHelpers::ImageCheckerHelpers($this->thumbnail);
        // $thumbnail = $this->getRawOriginal('thumbnail');

        // if (!$thumbnail) {
        //     return null;
        // }

        // if (env('APP_ENV') === 'local') {
        //     return env('APP_URL') . Storage::url($thumbnail);
        // } else {
        //     return url(Storage::url($thumbnail));
        // }
    }

    public function getTagsParsedAttribute()
    {
        return json_decode($this->getRawOriginal('tags'));
    }

    public function author()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function komentar()
    {
        return $this->hasMany(BlogComment::class, 'blog_id', 'id')->whereNull('parent_comment_id')->orderBy('created_at', 'desc');
    }
}
