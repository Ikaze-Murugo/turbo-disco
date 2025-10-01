<?php

namespace App\Notifications;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PropertyApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $property;

    /**
     * Create a new notification instance.
     */
    public function __construct(Property $property)
    {
        $this->property = $property;
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
        $propertyUrl = route('properties.show', $this->property->id);
        
        return (new MailMessage)
            ->subject('🎉 Your Property Has Been Approved - Murugo Property Platform')
            ->greeting('Congratulations!')
            ->line("Great news! Your property listing **'{$this->property->title}'** has been approved and is now live on our platform.")
            ->line("**Property Details:**")
            ->line("• **Title:** {$this->property->title}")
            ->line("• **Location:** {$this->property->location}")
            ->line("• **Price:** {$this->property->price} RWF")
            ->line("• **Type:** {$this->property->type}")
            ->line("• **Status:** ✅ Approved and Live")
            ->action('View Your Property', $propertyUrl)
            ->line('Your property is now visible to potential renters and you can start receiving inquiries!')
            ->line('**What happens next?**')
            ->line('• Renters can now view and inquire about your property')
            ->line('• You will receive notifications when someone shows interest')
            ->line('• You can manage your property listing from your dashboard')
            ->line('Thank you for using Murugo Property Platform!')
            ->salutation('Best regards, The Murugo Team');
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
            'notification_type' => 'property_approved',
            'message' => "Your property '{$this->property->title}' has been approved and is now live!",
        ];
    }
}
