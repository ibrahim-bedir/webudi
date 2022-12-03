<?php

namespace App\Console\Commands;

use App\Jobs\GetArtistsJob;
use App\Mail\NewTrackMail;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Throwable;

class SyncSpotifyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:spotify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Spotify Datas From Spotify Api';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (Queue::size() > 0) {
            $this->info('Queue is not empty, skipping sync');

            return Command::SUCCESS;
        }

        $batch = $this->createBatch();

        Cache::put('added_track_count', 0);
        Cache::put('sync_track_batch_id', $batch->id);

        GetArtistsJob::dispatch();

        $this->info('Datas queued for sync');

        return Command::SUCCESS;
    }

    private function createBatch(): Batch
    {
        return Bus::batch([])
           ->name('Sync Tracks')
           ->allowFailures()
           ->then(function () {
               $trackCount = Cache::get('added_track_count', 0);

               if ($trackCount > 0) {
                   Mail::send(new NewTrackMail($trackCount));
               } else {
                   logger()->info('No new tracks found');
               }
           })
           ->catch(function (Batch $batch, Throwable $e) {
               logger()->error('Something went wrong while syncing tracks: '.$e->getMessage());
           })
           ->dispatch();
    }
}
