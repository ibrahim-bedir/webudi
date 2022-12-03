<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory, HasUuid;

    public $fillable = [
        'name',
        'slug',
    ];

    public function artists()
    {
        return $this->belongsToMany(Artist::class);
    }
}
