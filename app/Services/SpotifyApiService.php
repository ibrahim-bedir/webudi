<?php

namespace App\Services;

use App\Contracts\SpotifyApiContract;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SpotifyApiService implements SpotifyApiContract
{
    private const SPOTIFY_API_TOKEN_URL = 'https://accounts.spotify.com/api/token';

    /**
     * @var \Illuminate\Http\Client\PendingRequest
     */
    private PendingRequest $http;

    /**
     * @var string
     */
    private string $clientId;

    /**
     * @var string
     */
    private string $clientSecret;

    /**
     * @var string
     */
    private string $token;

    /**
     * @var string
     */
    private string $spotifyEndpoint;

    /**
     * @var string
     */
    public string $artistIds = '';

    public function __construct(string $spotifyEndpoint, string $clientId, string $clientSecret)
    {
        $this->spotifyEndpoint = $spotifyEndpoint;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->token = $this->createToken();
        $this->http = $this->createHttpClient();
        $this->getSearchArtists();
    }

    /**
     * @return string
     */
    private function createToken(): string
    {
        $basicToken = base64_encode($this->clientId.':'.$this->clientSecret);

        return Http::asForm()
            ->acceptJson()
            ->withToken($basicToken, 'Basic')
            ->post(self::SPOTIFY_API_TOKEN_URL, [
                'grant_type' => 'client_credentials',
            ])
            ->json('access_token');
    }

    /**
     * @return \Illuminate\Http\Client\PendingRequest
     */
    private function createHttpClient(): PendingRequest
    {
        return Http::baseUrl($this->spotifyEndpoint)
            ->acceptJson()
            ->contentType('application/json')
            ->withToken($this->token)
            ->withoutVerifying();
    }

    /**
     * @return void
     */
    public function getSearchArtists(): void
    {
        $artists = $this->http->get('search', [
            'q' => 'artist',
            'type' => 'artist',
            'limit' => 10,
        ])->json('artists.items');

        foreach ($artists as $artist) {
            $this->artistIds = ltrim($this->artistIds.','.$artist['id'], ',');
        }
    }

    /**
     * @return \Illuminate\Http\Client\Response
     */
    public function getArtists(): Response
    {
        return  $this->http->get('artists', [
            'ids' => $this->artistIds,
            'market' => 'TR',
            'limit' => 50,
        ]);
    }

    /**
     * @param  string  $artistId
     * @return \Illuminate\Http\Client\Response
     */
    public function getAlbums(string $artistId): Response
    {
        return $this->http->get('artists/'.$artistId.'/albums', [
            'market' => 'TR',
            'limit' => 50,
        ]);
    }

    /**
     * @param  string  $albumId
     * @return \Illuminate\Http\Client\Response
     */
    public function getTracks(string $albumId): Response
    {
        return $this->http->get('albums/'.$albumId.'/tracks', [
            'market' => 'TR',
            'limit' => 50,
        ]);
    }

    /**
     * @return bool
     */
    public function isTooManyAttempts(): bool
    {
        return Cache::has(self::CACHE_RATE_LIMIT_KEY);
    }

    /**
     * @param  \Illuminate\Http\Client\Response|null  $response
     * @return int
     */
    public function getRetryDelay(?Response $response = null): int
    {
        if ($response && $retryAfter = $response->header('Retry-After')) {
            logger()->info('Spotify API rate limit exceeded. Waiting '.$retryAfter.' seconds before retrying.');
            Cache::put(self::CACHE_RATE_LIMIT_KEY, now()->addSeconds($retryAfter)->timestamp, $retryAfter);

            return $retryAfter;
        }

        if (Cache::has(self::CACHE_RATE_LIMIT_KEY)) {
            return Cache::get(self::CACHE_RATE_LIMIT_KEY) - now()->timestamp;
        }

        return self::JOB_RELEASE_DELAY;
    }
}
