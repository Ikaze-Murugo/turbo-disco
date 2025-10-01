<?php

namespace App\Jobs;

use App\Models\EmailCampaign;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBulkEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public EmailCampaign $campaign
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $recipients = $this->getRecipients();
        
        foreach ($recipients as $user) {
            SendEmailJob::dispatch($this->campaign, $user);
        }

        $this->campaign->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }

    /**
     * Get recipients based on campaign criteria
     */
    private function getRecipients()
    {
        $query = User::query();

        switch ($this->campaign->target_audience) {
            case 'landlords':
                $query->where('role', 'landlord');
                break;
            case 'renters':
                $query->where('role', 'renter');
                break;
            case 'admin':
                $query->where('role', 'admin');
                break;
            case 'custom':
                // Apply custom criteria
                if (isset($this->campaign->target_criteria['location'])) {
                    $query->whereHas('properties', function($q) {
                        $q->where('location', $this->campaign->target_criteria['location']);
                    });
                }
                break;
        }

        return $query->get();
    }
}