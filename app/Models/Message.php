<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'recipient_id',
        'property_id',
        'subject',
        'body',
        'is_read',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Generate a conversation ID for two users
     */
    public static function generateConversationId($userId1, $userId2)
    {
        // Sort user IDs to ensure consistent conversation ID regardless of who sends first
        $sortedIds = [$userId1, $userId2];
        sort($sortedIds);
        return 'conv_' . $sortedIds[0] . '_' . $sortedIds[1];
    }

    /**
     * Get all messages in a conversation
     */
    public static function getConversationMessages($conversationId)
    {
        return static::where('conversation_id', $conversationId)
                    ->with(['sender', 'recipient', 'property'])
                    ->orderBy('created_at', 'asc')
                    ->get();
    }

    /**
     * Get conversation participants
     */
    public function getParticipants()
    {
        return User::whereIn('id', [$this->sender_id, $this->recipient_id])->get();
    }
}