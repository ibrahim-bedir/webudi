<?php

namespace App\Jobs;

use App\Contracts\SpotifyApiContract;
use App\Models\Album;
use App\Models\Artist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class GetTracksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \App\Models\Artist
     */
    public Artist $artist;

    /**
     * @var \App\Models\Album
     */
    public Album $album;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\Artist  $artist
     * @param  \App\Models\Album  $album
     * @return void
     */
    public function __construct(Artist $artist, Album $album)
    {
        $this->artist = $artist;
        $this->album = $album;
    }

    /**
     * Execute the job.
     *
     * @param  \App\Contracts\SpotifyApiContract  $spotifyApiService
     * @return void
     */
    public function handle(SpotifyApiContract $spotifyApiService)
    {
        if ($spotifyApiService->isTooManyAttempts()) {
            return $this->release($spotifyApiService->getRetryDelay());
        }

        $response = $spotifyApiService->getTracks($this->album->spotify_id);

        if ($response->failed()) {
            return $this->release($spotifyApiService->getRetryDelay($response));
        }

        $batchId = Cache::get('sync_track_batch_id');

        if (empty($batchId)) {
            return $this->fail(new RuntimeException('Batch ID not found in cache.'));
        }

        $batch = Bus::findBatch($batchId);

        foreach ($response->json('items') as $trackItem) {
            $batch->add(new SyncTrackJob($trackItem, $this->artist, $this->album));
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
