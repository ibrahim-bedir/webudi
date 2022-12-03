<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArtistResource;
use App\Http\Resources\GenreResource;
use App\Http\Resources\TrackResource;
use App\Models\Genre;
use App\Models\Track;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GenreController extends Controller
{
    /**
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return GenreResource::collection(Genre::paginate(10));
    }

    /**
     * @param  \App\Models\Genre  $genre
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function genreTracks(Genre $genre): ResourceCollection
    {
        $tracks = Track::query()
            ->whereHas('artists', function ($query) use ($genre) {
                $query->whereIn('artist_id', $genre->artists()->pluck('id'));
            })
            ->paginate(10);

        return TrackResource::collection($tracks);
    }

    /**
     * @param  \App\Models\Genre  $genre
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function genreArtists(Genre $genre): ResourceCollection
    {
        return ArtistResource::collection($genre->artists()->paginate(10));
    }
}
