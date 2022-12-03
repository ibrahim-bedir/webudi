<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Track extends Model
{
    use HasFactory, HasUuid;

    public $fillable = [
        'name',
        'spotify_id',
        'album_id',
        'preview_url',
        'duration_ms',
        'track_number',
        'type',
        'mail_sended_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class);
    }
}
