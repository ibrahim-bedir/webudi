<?php

namespace App\Jobs;

use App\Models\Album;
use App\Models\Artist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncAlbumJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    public array $albumItem;

    /**
     * @var \App\Models\Artist
     */
    public Artist $artist;

    /**
     * Create a new job instance.
     *
     * @param  array  $albumItem
     * @param  \App\Models\Artist  $artist
     * @return void
     */
    public function __construct(array $albumItem, Artist $artist)
    {
        $this->albumItem = $albumItem;
        $this->artist = $artist;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $album = Album::firstOrCreate([
            'name' => $this->albumItem['name'],
            'spotify_id' => $this->albumItem['id'],
            'release_date' => $this->albumItem['release_date'],
            'total_tracks' => $this->albumItem['total_tracks'],
            'type' => $this->albumItem['type'],
        ]);

        $this->artist->albums()->syncWithoutDetaching($album->id);

        GetTracksJob::dispatch($this->artist, $album);
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
