<?php

namespace App\Jobs;

use App\Contracts\SpotifyApiContract;
use App\Models\Artist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetAlbumsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \App\Models\Artist
     */
    public Artist $artist;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\Artist  $artist
     * @return void
     */
    public function __construct(Artist $artist)
    {
        $this->artist = $artist;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SpotifyApiContract $spotifyApiService)
    {
        if ($spotifyApiService->isTooManyAttempts()) {
            return $this->release($spotifyApiService->getRetryDelay());
        }

        $response = $spotifyApiService->getAlbums($this->artist->spotify_id);

        if ($response->failed()) {
            return $this->release($spotifyApiService->getRetryDelay($response));
        }

        foreach ($response->json('items') as $albumItem) {
            SyncAlbumJob::dispatch($albumItem, $this->artist);
        }
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil(): \DateTime
    {
        return now()->addDay();
    }
}
