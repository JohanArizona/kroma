<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comic extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'author',
        'synopsis',
        'status',
        'cover_url',
        'banner_url',
        'created_by',
        'updated_by',
    ];

    // Relasi Many-to-Many ke tabel genres melalui comic_genre
    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'comic_genre');
    }
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function readingHistories()
    {
        return $this->hasMany(ReadingHistory::class);
    }
}
