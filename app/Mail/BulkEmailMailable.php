<?php

namespace App\Mail;

use App\Models\EmailCampaign;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BulkEmailMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public EmailCampaign $campaign,
        public User $user
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->processTemplate($this->campaign->subject),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $content = $this->processTemplate($this->campaign->content);
        
        return new Content(
            view: 'emails.bulk-email',
            with: [
                'content' => $content,
                'user' => $this->user,
                'campaign' => $this->campaign,
                'unsubscribeLink' => route('email.unsubscribe', $this->user->id),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Process template variables
     */
    private function processTemplate(string $template): string
    {
        $variables = [
            '{{user_name}}' => $this->user->name,
            '{{user_email}}' => $this->user->email,
            '{{platform_name}}' => config('app.name'),
            '{{unsubscribe_link}}' => route('email.unsubscribe', $this->user->id),
            '{{current_date}}' => now()->format('F j, Y'),
            '{{current_year}}' => now()->year,
        ];

        return str_replace(array_keys($variables), array_values($variables), $template);
    }
}