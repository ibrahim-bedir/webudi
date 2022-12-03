<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArtistResource;
use App\Http\Resources\TrackResource;
use App\Models\Artist;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ArtistController extends Controller
{
    /**
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return ArtistResource::collection(Artist::paginate(10));
    }

    /**
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function artistTracks(Artist $artist): ResourceCollection
    {
        return TrackResource::collection($artist->tracks()->paginate(10));
    }
}
