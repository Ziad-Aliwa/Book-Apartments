@extends('layouts')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-shield-lock-fill text-warning me-2"></i>Platform Admin Dashboard</h2>
            <span class="text-muted small">System statistics, user activity, and apartment moderation</span>
        </div>
        <a href="{{ route('admin.apartments.pending') }}" class="btn btn-warning fw-bold position-relative">
            <i class="bi bi-hourglass-split me-1"></i>Review Pending Apartments
            @if($pendingApartments > 0)
                <span class="badge bg-danger ms-1">{{ $pendingApartments }}</span>
            @endif
        </a>
    </div>

    <!-- Platform Stats -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-3 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted fw-bold d-block">REGISTERED USERS</small>
                        <span class="fs-3 fw-bold text-dark">{{ $totalUsers }}</span>
                    </div>
                    <div class="fs-1 text-primary"><i class="bi bi-people-fill"></i></div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-3 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted fw-bold d-block">ACTIVE LISTINGS</small>
                        <span class="fs-3 fw-bold text-success">{{ $approvedApartments }}</span>
                        <small class="text-warning d-block">({{ $pendingApartments }} pending)</small>
                    </div>
                    <div class="fs-1 text-success"><i class="bi bi-buildings"></i></div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-3 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted fw-bold d-block">TOTAL BOOKINGS</small>
                        <span class="fs-3 fw-bold text-dark">{{ $totalBookings }}</span>
                        <small class="text-success d-block">{{ $confirmedBookings }} confirmed</small>
                    </div>
                    <div class="fs-1 text-info"><i class="bi bi-calendar-check-fill"></i></div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-3 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted fw-bold d-block">PROCESSED VOLUME</small>
                        <span class="fs-3 fw-bold text-success">${{ number_format($platformVolume, 2) }}</span>
                    </div>
                    <div class="fs-1 text-success"><i class="bi bi-currency-dollar"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Listings Waiting for Approval -->
    <div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
        <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-hourglass-split text-warning me-2"></i>Apartments Awaiting Moderation</h5>
            <a href="{{ route('admin.apartments.pending') }}" class="btn btn-sm btn-outline-warning">View All</a>
        </div>
        <div class="card-body p-0">
            @if($recentPending->isEmpty())
                <div class="py-5 text-center text-muted">
                    <i class="bi bi-check-circle fs-1 text-success d-block mb-2"></i>
                    All listings are moderated! No pending approvals.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Listing</th>
                                <th>Host</th>
                                <th>Address</th>
                                <th>Price / Night</th>
                                <th>Available Window</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPending as $apartment)
                                <tr>
                                    <td>
                                        <strong>{{ $apartment->title }}</strong>
                                    </td>
                                    <td>
                                        {{ $apartment->user->name }}
                                        <small class="d-block text-muted">{{ $apartment->user->email }}</small>
                                    </td>
                                    <td>{{ $apartment->address }}</td>
                                    <td><strong>${{ number_format($apartment->price_per_night, 2) }}</strong></td>
                                    <td>{{ $apartment->available_from }} &rarr; {{ $apartment->available_to }}</td>
                                    <td>
                                        <form action="{{ route('admin.apartments.approve', $apartment->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-success fw-bold me-1">
                                                <i class="bi bi-check-lg"></i> Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.apartments.destroy', $apartment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Reject and delete this listing?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i> Reject
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
