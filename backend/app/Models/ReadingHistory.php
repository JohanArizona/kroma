<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ReadingHistory extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'comic_id',
        'chapter_id',
        'last_read_at',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comic()
    {
        return $this->belongsTo(Comic::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
