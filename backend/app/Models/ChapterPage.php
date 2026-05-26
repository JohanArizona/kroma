<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChapterPage extends Model
{
    protected $fillable = [
        'chapter_id',
        'page_number',
        'image_url',
    ];
}