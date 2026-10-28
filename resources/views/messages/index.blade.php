@extends('layouts')

@section('title', 'Inbox Messages')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0"><i class="bi bi-chat-dots-fill text-primary me-2"></i>Inbox Messages</h3>
                <span class="text-muted small">Direct chat between guests and apartment hosts</span>
            </div>

            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                @if($conversations->isEmpty())
                    <div class="card-body py-5 text-center text-muted">
                        <i class="bi bi-chat-left-quote fs-1 d-block mb-3"></i>
                        <h5 class="fw-bold">No conversations yet</h5>
                        <p class="mb-3">You can message apartment hosts directly by clicking "Contact Host" on any apartment page.</p>
                        <a href="{{ route('apartments.index') }}" class="btn btn-primary btn-sm">Find Apartments</a>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($conversations as $conversation)
                            @php
                                $otherUser = $conversation->otherUser(Auth::id());
                                $lastMessage = $conversation->latestMessage;
                            @endphp
                            <a href="{{ route('messages.show', $conversation->id) }}" class="list-group-item list-group-item-action p-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5" style="width: 50px; height: 50px;">
                                        {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="d-flex justify-content-between align-items-baseline mb-1">
                                            <h6 class="fw-bold mb-0 text-dark">{{ $otherUser->name }}</h6>
                                            <small class="text-muted">{{ $lastMessage ? $lastMessage->created_at->diffForHumans() : $conversation->created_at->diffForHumans() }}</small>
                                        </div>
                                        @if($conversation->apartment)
                                            <small class="text-primary d-block text-truncate mb-1">
                                                <i class="bi bi-house me-1"></i>{{ $conversation->apartment->title }}
                                            </small>
                                        @endif
                                        <p class="text-muted small text-truncate mb-0">
                                            {{ $lastMessage ? $lastMessage->body : 'Conversation started.' }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $conversations->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
