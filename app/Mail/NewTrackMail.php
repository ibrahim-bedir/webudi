<?php

namespace App\Mail;

use App\Http\Middleware\RateLimitedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Throwable;

class NewTrackMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var int
     */
    public int $trackCount = 0;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(int $trackCount)
    {
        $this->trackCount = $trackCount;
    }

    public function middleware()
    {
        return [
            new RateLimitedMail('track-created-mail', 4, 10, 15),
        ];
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            from: env('MAIL_FROM_ADDRESS'),
            to: env('MAIL_FROM_ADDRESS'),
            subject: 'New Track(s) Added',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.email',
            with: [
                'trackCount' => $this->trackCount,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addDay();
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(Throwable $e)
    {
        logger()->error('Mail sending error: '.$e->getMessage());
    }
}
