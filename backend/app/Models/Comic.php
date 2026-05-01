<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comic extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    // Relasi Many-to-Many ke tabel genres melalui comic_genre
    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'comic_genre');
    }
}