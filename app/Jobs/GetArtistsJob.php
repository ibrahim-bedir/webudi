<?php

namespace App\Jobs;

use App\Contracts\SpotifyApiContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetArtistsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

        $response = $spotifyApiService->getArtists();

        if ($response->failed()) {
            return $this->release($spotifyApiService->getRetryDelay($response));
        }

        foreach ($response->json('artists') as $artistItem) {
            SyncArtistJob::dispatch($artistItem);
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
