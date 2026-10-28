@extends('layouts')

@section('title', 'Notifications')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0"><i class="bi bi-bell-fill text-warning me-2"></i>Your Notifications</h3>
                @if(Auth::user()->unreadNotifications->isNotEmpty())
                    <form action="{{ route('notifications.mark_all_read') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-check2-all me-1"></i>Mark all as read
                        </button>
                    </form>
                @endif
            </div>

            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                @if($notifications->isEmpty())
                    <div class="card-body py-5 text-center text-muted">
                        <i class="bi bi-bell-slash fs-1 d-block mb-3"></i>
                        <p class="mb-0">No notifications yet. You'll be notified of new bookings and updates here.</p>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notification)
                            @php
                                $data = $notification->data;
                                $icon = match($data['type'] ?? '') {
                                    'new_booking' => 'bi-calendar-plus text-primary',
                                    'booking_status' => 'bi-ticket-detailed text-success',
                                    'apartment_approved' => 'bi-check-circle-fill text-success',
                                    default => 'bi-info-circle text-info'
                                };
                            @endphp
                            <div class="list-group-item p-3 {{ $notification->read_at ? 'bg-white' : 'bg-light border-start border-4 border-primary' }}">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="fs-4 mt-1"><i class="bi {{ $icon }}"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong class="text-dark">{{ $data['title'] ?? 'Notification' }}</strong>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-2 text-secondary small">{{ $data['message'] ?? '' }}</p>
                                        @if(!empty($data['url']))
                                            <a href="{{ $data['url'] }}" class="btn btn-sm btn-outline-primary py-0">View Details &rarr;</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $notifications->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
