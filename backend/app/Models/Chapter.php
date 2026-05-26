<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Chapter extends Model
{
    use HasUuids;

    protected $fillable = [
        'comic_id',
        'chapter_number',
        'title',
        'created_by',
        'updated_by',
    ];

    public function comic()
    {
        return $this->belongsTo(Comic::class);
    }

    public function pages()
    {
        return $this->hasMany(ChapterPage::class)->orderBy('page_number');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function readingHistories()
    {
        return $this->hasMany(ReadingHistory::class);
    }
}