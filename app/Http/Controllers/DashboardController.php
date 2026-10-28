<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function owner()
    {
        $user = Auth::user();
        $apartments = $user->apartments()->with(['bookings.user', 'images'])->get();
        $apartmentIds = $apartments->pluck('id');

        $totalApartments = $apartments->count();
        $allBookings = Booking::whereIn('apartment_id', $apartmentIds)->with(['user', 'apartment'])->latest()->get();
        
        $totalBookings = $allBookings->count();
        $pendingBookingsCount = $allBookings->where('status', 'pending')->count();
        $totalEarnings = $allBookings->where('payment_status', 'paid')->sum('total_price');

        $recentBookings = $allBookings->take(6);

        return view('dashboard.owner', compact(
            'apartments',
            'totalApartments',
            'totalBookings',
            'pendingBookingsCount',
            'totalEarnings',
            'recentBookings'
        ));
    }

    public function admin()
    {
        $totalUsers = User::count();
        $totalApartments = Apartment::count();
        $approvedApartments = Apartment::where('admin_approve', true)->count();
        $pendingApartments = Apartment::where('admin_approve', false)->count();

        $totalBookings = Booking::count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $canceledBookings = Booking::where('status', 'canceled')->count();

        $platformVolume = Booking::where('payment_status', 'paid')->sum('total_price');

        $recentPending = Apartment::where('admin_approve', false)->with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalApartments',
            'approvedApartments',
            'pendingApartments',
            'totalBookings',
            'confirmedBookings',
            'pendingBookings',
            'canceledBookings',
            'platformVolume',
            'recentPending'
        ));
    }
}
