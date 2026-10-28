@extends('layouts')

@section('title', 'Chat with ' . $conversation->otherUser(Auth::id())->name)

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden d-flex flex-column" style="height: 75vh;">
                <!-- Chat Header -->
                <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('messages.index') }}" class="text-white me-1" title="Back to Inbox">
                            <i class="bi bi-arrow-left fs-5"></i>
                        </a>
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 42px; height: 42px;">
                            {{ strtoupper(substr($conversation->otherUser(Auth::id())->name, 0, 1)) }}
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">{{ $conversation->otherUser(Auth::id())->name }}</h6>
                            <small class="text-light opacity-75">{{ $conversation->otherUser(Auth::id())->email }}</small>
                        </div>
                    </div>

                    @if($conversation->apartment)
                        <div class="text-end d-none d-sm-block">
                            <a href="{{ route('apartments.show', $conversation->apartment->id) }}" class="btn btn-sm btn-outline-light">
                                <i class="bi bi-house me-1"></i>{{ Str::limit($conversation->apartment->title, 25) }}
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Messages Window -->
                <div class="card-body p-4 overflow-auto flex-grow-1 bg-light" id="messagesContainer">
                    @forelse($conversation->messages as $msg)
                        @php
                            $isMe = ($msg->sender_id === Auth::id());
                        @endphp
                        <div class="d-flex mb-3 {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}">
                            <div class="card border-0 shadow-sm p-3 rounded-4 {{ $isMe ? 'bg-primary text-white' : 'bg-white text-dark' }}" style="max-width: 75%;">
                                <div class="small fw-semibold mb-1 {{ $isMe ? 'text-light opacity-75' : 'text-muted' }}">
                                    {{ $isMe ? 'You' : $msg->sender->name }}
                                </div>
                                <p class="mb-1" style="white-space: pre-wrap;">{{ $msg->body }}</p>
                                <div class="text-end small {{ $isMe ? 'text-light opacity-75' : 'text-muted' }}" style="font-size: 0.75rem;">
                                    {{ $msg->created_at->format('M d, H:i') }}
                                    @if($isMe)
                                        <i class="bi {{ $msg->read_at ? 'bi-check2-all text-info' : 'bi-check2' }}"></i>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-chat-heart fs-1 d-block mb-2"></i>
                            Start the conversation by sending a message below.
                        </div>
                    @endforelse
                </div>

                <!-- Message Input Footer -->
                <div class="card-footer bg-white p-3 border-top">
                    <form action="{{ route('messages.store', $conversation->id) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <textarea name="body" class="form-control rounded-pill px-3" rows="1" placeholder="Type your message here..." required autofocus></textarea>
                        <button type="submit" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('messagesContainer');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
</script>
@endpush
