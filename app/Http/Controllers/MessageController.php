<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        // Get all conversations for the current user
        $conversations = Message::where('sender_id', Auth::id())
                              ->orWhere('recipient_id', Auth::id())
                              ->select('conversation_id')
                              ->distinct()
                              ->pluck('conversation_id');

        // Get the latest message for each conversation
        $latestMessages = collect();
        foreach ($conversations as $conversationId) {
            $latestMessage = Message::where('conversation_id', $conversationId)
                                  ->with(['sender', 'recipient', 'property'])
                                  ->orderBy('created_at', 'desc')
                                  ->first();
            $latestMessages->push($latestMessage);
        }

        // Sort by latest message date
        $latestMessages = $latestMessages->sortByDesc('created_at');

        return view('messages.index', compact('latestMessages'));
    }

    public function create(Property $property)
    {
        // Only renters can message landlords
        if (!Auth::user()->isRenter()) {
            abort(403);
        }

        return view('messages.create', compact('property'));
    }

    public function store(Request $request, Property $property)
    {
        if (!Auth::user()->isRenter()) {
            abort(403);
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
        ]);

        $conversationId = Message::generateConversationId(Auth::id(), $property->landlord_id);

        Message::create([
            'conversation_id' => $conversationId,
            'sender_id' => Auth::id(),
            'recipient_id' => $property->landlord_id,
            'property_id' => $property->id,
            'subject' => $validated['subject'],
            'body' => $validated['body'],
        ]);

        return redirect()->route('properties.show', $property)
                        ->with('success', 'Message sent successfully!');
    }

    public function show(Message $message)
    {
        // Check if user is sender or recipient
        if ($message->sender_id !== Auth::id() && $message->recipient_id !== Auth::id()) {
            abort(403);
        }

        // Get all messages in this conversation
        $conversationMessages = Message::getConversationMessages($message->conversation_id);

        // Mark all unread messages in this conversation as read if user is recipient
        foreach ($conversationMessages as $msg) {
            if ($msg->recipient_id === Auth::id() && !$msg->is_read) {
                $msg->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
            }
        }

        // Get the other participant in the conversation
        $otherParticipant = null;
        foreach ($conversationMessages as $msg) {
            if ($msg->sender_id !== Auth::id()) {
                $otherParticipant = $msg->sender;
                break;
            } elseif ($msg->recipient_id !== Auth::id()) {
                $otherParticipant = $msg->recipient;
                break;
            }
        }

        return view('messages.show', compact('conversationMessages', 'otherParticipant'));
    }

    public function reply(Request $request, Message $message)
    {
        // Check if user is part of this conversation (either sender or recipient)
        if ($message->sender_id !== Auth::id() && $message->recipient_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        // Determine the recipient (the other person in the conversation)
        $recipientId = ($message->sender_id === Auth::id()) ? $message->recipient_id : $message->sender_id;

        // Create a reply message using the same conversation ID
        Message::create([
            'conversation_id' => $message->conversation_id,
            'sender_id' => Auth::id(),
            'recipient_id' => $recipientId,
            'property_id' => $message->property_id,
            'subject' => 'Re: ' . $message->subject,
            'body' => $validated['body'],
        ]);

        return redirect()->route('messages.show', $message)
                        ->with('success', 'Message sent successfully!');
    }
}