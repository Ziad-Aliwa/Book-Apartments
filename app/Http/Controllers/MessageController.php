<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $conversations = Conversation::where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->with(['apartment.images', 'userOne', 'userTwo', 'latestMessage'])
            ->latest('updated_at')
            ->paginate(15);

        return view('messages.index', compact('conversations'));
    }

    public function start(Apartment $apartment)
    {
        $currentUserId = Auth::id();
        $hostId = $apartment->user_id;

        if ($currentUserId === $hostId) {
            return back()->with('error', 'You cannot start a chat with yourself.');
        }

        // Find existing conversation between these two users for this apartment or create new
        $conversation = Conversation::where(function ($q) use ($currentUserId, $hostId) {
            $q->where('user_one_id', $currentUserId)->where('user_two_id', $hostId);
        })->orWhere(function ($q) use ($currentUserId, $hostId) {
            $q->where('user_one_id', $hostId)->where('user_two_id', $currentUserId);
        })->where('apartment_id', $apartment->id)->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'apartment_id' => $apartment->id,
                'user_one_id' => $currentUserId,
                'user_two_id' => $hostId,
            ]);
        }

        return redirect()->route('messages.show', $conversation->id);
    }

    public function show(Conversation $conversation)
    {
        $userId = Auth::id();

        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            abort(403);
        }

        // Mark incoming messages as read
        $conversation->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $conversation->load(['apartment.images', 'userOne', 'userTwo', 'messages.sender']);

        return view('messages.show', compact('conversation'));
    }

    public function store(Request $request, Conversation $conversation)
    {
        $userId = Auth::id();

        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            abort(403);
        }

        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $conversation->messages()->create([
            'sender_id' => $userId,
            'body' => $request->body,
        ]);

        $conversation->touch(); // updates updated_at

        return redirect()->route('messages.show', $conversation->id);
    }
}
