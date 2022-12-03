<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory, HasUuid;

    public $fillable = [
        'name',
        'spotify_id',
        'release_date',
        'total_tracks',
        'type',
    ];

    public function artists()
    {
        return $this->belongsToMany(Artist::class);
    }
}
