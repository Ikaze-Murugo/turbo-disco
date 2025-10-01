<?php

namespace App\Jobs;

use App\Mail\BulkEmailMailable;
use App\Models\EmailCampaign;
use App\Models\EmailRecipient;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public EmailCampaign $campaign,
        public User $user
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Check user email preferences
        if (!$this->shouldSendEmail()) {
            return;
        }

        // Create recipient record
        $recipient = EmailRecipient::create([
            'campaign_id' => $this->campaign->id,
            'user_id' => $this->user->id,
            'email' => $this->user->email,
            'status' => 'pending'
        ]);

        try {
            // Send email
            Mail::to($this->user->email)->send(new BulkEmailMailable($this->campaign, $this->user));
            
            $recipient->markAsSent();
            
            // Update campaign delivered count
            $this->campaign->increment('delivered_count');

        } catch (\Exception $e) {
            $recipient->markAsFailed($e->getMessage());
            
            // Log the error
            \Log::error('Email sending failed', [
                'campaign_id' => $this->campaign->id,
                'user_id' => $this->user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check if email should be sent based on user preferences
     */
    private function shouldSendEmail(): bool
    {
        $preferences = $this->user->emailPreferences;
        
        if (!$preferences) {
            return true; // Default to sending if no preferences set
        }

        // Get template category
        $category = $this->campaign->template?->category ?? 'system';

        return $preferences->shouldReceiveEmail($category);
    }
}