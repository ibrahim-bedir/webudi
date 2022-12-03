<?php

namespace App\Jobs;

use App\Models\Artist;
use App\Models\Genre;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class SyncArtistJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    public array $artistItem;

    /**
     * Create a new job instance.
     *
     * @param  array  $artistItem
     * @return void
     */
    public function __construct(array $artistItem)
    {
        $this->artistItem = $artistItem;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $artist = Artist::firstOrCreate([
            'spotify_id' => $this->artistItem['id'],
            'name' => $this->artistItem['name'],
            'popularity' => $this->artistItem['popularity'],
            'type' => $this->artistItem['type'],
        ]);

        if (isset($this->artistItem['genres']) && count($this->artistItem['genres']) > 0) {
            foreach ($this->artistItem['genres'] as $genreItem) {
                $genre = Genre::firstOrCreate([
                    'name' => $genreItem,
                    'slug' => Str::slug($genreItem),
                ]);

                $artist->genres()->syncWithoutDetaching($genre->id);
            }
        }

        GetAlbumsJob::dispatch($artist);
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
