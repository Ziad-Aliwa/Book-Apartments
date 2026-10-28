<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Notifications\ApartmentApprovedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminApartmentController extends Controller
{
    public function index()
    {
        $apartments = Apartment::where('admin_approve', false)->with(['user', 'images'])->latest()->get();
        return view('admin.apartments.pending', compact('apartments'));
    }

    public function approve(Apartment $apartment)
    {
        $apartment->admin_approve = true;
        $apartment->save();

        if ($apartment->user) {
            $apartment->user->notify(new ApartmentApprovedNotification($apartment));
        }

        return redirect()->route('admin.apartments.pending')->with('success', 'Apartment approved successfully.');
    }

    public function destroy(Apartment $apartment)
    {
        foreach ($apartment->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }

        $apartment->delete();
        return redirect()->route('admin.apartments.pending')->with('success', 'Apartment rejected and deleted.');
    }
}
