<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];
    const UPDATED_AT = null;

    public function comic()
    {
        return $this->belongsTo(Comic::class);
    }
}