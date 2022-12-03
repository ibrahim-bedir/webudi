<?php

namespace App\Contracts;

use Illuminate\Http\Client\Response;

interface SpotifyApiContract
{
    public const JOB_RELEASE_DELAY = 60;

    public const CACHE_RATE_LIMIT_KEY = 'spotify_api_rate_limit';

    public function getSearchArtists(): void;

    public function getArtists(): Response;

    public function getAlbums(string $artistId): Response;

    public function getTracks(string $albumId): Response;

    public function isTooManyAttempts(): bool;

    public function getRetryDelay(?Response $response = null): int;
}
