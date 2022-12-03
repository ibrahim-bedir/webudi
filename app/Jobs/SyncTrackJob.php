<?php

namespace App\Jobs;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Track;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class SyncTrackJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    public array $trackItem;

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
     * @param  array  $trackItem
     * @param  \App\Models\Artist  $artist
     * @param  \App\Models\Album  $album
     * @return void
     */
    public function __construct(array $trackItem, Artist $artist, Album $album)
    {
        $this->trackItem = $trackItem;
        $this->artist = $artist;
        $this->album = $album;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        $track = Track::firstOrCreate([
            'name' => $this->trackItem['name'],
            'album_id' => $this->album->id,
            'spotify_id' => $this->trackItem['id'],
            'preview_url' => $this->trackItem['preview_url'] ?? 'None',
            'duration_ms' => $this->trackItem['duration_ms'],
            'track_number' => $this->trackItem['track_number'],
            'type' => $this->trackItem['type'],
        ]);

        $track->artists()->syncWithoutDetaching($this->artist->id);

        if ($track->wasRecentlyCreated) {
            Cache::increment('added_track_count');
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
