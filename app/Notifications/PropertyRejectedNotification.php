<?php

namespace App\Notifications;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PropertyRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $property;
    protected $rejectionReason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Property $property, string $rejectionReason = null)
    {
        $this->property = $property;
        $this->rejectionReason = $rejectionReason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $editUrl = route('properties.edit', $this->property->id);
        
        $mailMessage = (new MailMessage)
            ->subject('Property Listing Requires Attention - Murugo Property Platform')
            ->greeting('Hello!')
            ->line("We've reviewed your property listing **'{$this->property->title}'** and it requires some adjustments before it can be approved.")
            ->line("**Property Details:**")
            ->line("• **Title:** {$this->property->title}")
            ->line("• **Location:** {$this->property->location}")
            ->line("• **Price:** {$this->property->price} RWF")
            ->line("• **Type:** {$this->property->type}")
            ->line("• **Status:** ⚠️ Requires Revision");

        if ($this->rejectionReason) {
            $mailMessage->line("**Reason for Revision:**")
                       ->line($this->rejectionReason);
        }

        $mailMessage->action('Edit Your Property', $editUrl)
                   ->line('**What you need to do:**')
                   ->line('• Review the feedback above')
                   ->line('• Make the necessary changes to your property listing')
                   ->line('• Resubmit your property for review')
                   ->line('• Our team will review it again within 24-48 hours')
                   ->line('**Need help?**')
                   ->line('If you have any questions about the feedback or need assistance, please don\'t hesitate to contact our support team.')
                   ->line('Thank you for your patience and for using Murugo Property Platform!')
                   ->salutation('Best regards, The Murugo Team');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'property_id' => $this->property->id,
            'property_title' => $this->property->title,
            'property_location' => $this->property->location,
            'property_price' => $this->property->price,
            'rejection_reason' => $this->rejectionReason,
            'notification_type' => 'property_rejected',
            'message' => "Your property '{$this->property->title}' requires revision. Please check the feedback and resubmit.",
        ];
    }
}
