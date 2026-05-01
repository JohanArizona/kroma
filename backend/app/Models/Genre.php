<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    // Relasi Many-to-Many kembali ke comics
    public function comics()
    {
        return $this->belongsToMany(Comic::class, 'comic_genre');
    }
}