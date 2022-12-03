<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory, HasUuid;

    public $fillable = [
        'name',
        'spotify_id',
        'popularity',
        'type',
    ];

    public function tracks()
    {
        return $this->belongsToMany(Track::class)->orderBy('track_number', 'asc');
    }

    public function albums()
    {
        return $this->belongsToMany(Album::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }
}
