<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApartmentBookingFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_book_their_own_apartment()
    {
        $owner = User::factory()->create();
        $apartment = Apartment::create([
            'user_id' => $owner->id,
            'title' => 'My Luxury Flat',
            'address' => 'Zamalek, Cairo',
            'price_per_night' => 100,
            'available_from' => now()->toDateString(),
            'available_to' => now()->addMonth()->toDateString(),
            'admin_approve' => true,
            'status' => 'available',
        ]);

        $response = $this->actingAs($owner)->post(route('bookings.store', $apartment->id), [
            'apartment_id' => $apartment->id,
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
        ]);

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_overlapping_confirmed_bookings_are_blocked()
    {
        $owner = User::factory()->create();
        $guest1 = User::factory()->create();
        $guest2 = User::factory()->create();

        $apartment = Apartment::create([
            'user_id' => $owner->id,
            'title' => 'Sea View Chalet',
            'address' => 'North Coast, Alexandria',
            'price_per_night' => 150,
            'available_from' => now()->toDateString(),
            'available_to' => now()->addMonth()->toDateString(),
            'admin_approve' => true,
            'status' => 'available',
        ]);

        // First booking confirmed
        Booking::create([
            'user_id' => $guest1->id,
            'apartment_id' => $apartment->id,
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(10)->toDateString(),
            'total_price' => 750,
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        // Second guest tries to book overlapping dates
        $response = $this->actingAs($guest2)->post(route('bookings.store', $apartment->id), [
            'apartment_id' => $apartment->id,
            'start_date' => now()->addDays(7)->toDateString(),
            'end_date' => now()->addDays(12)->toDateString(),
        ]);

        $response->assertSessionHasErrors('error');
        $this->assertEquals(1, Booking::count());
    }

    public function test_long_stay_discount_is_calculated()
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();

        $apartment = Apartment::create([
            'user_id' => $owner->id,
            'title' => 'Modern Studio',
            'address' => 'New Cairo',
            'price_per_night' => 100,
            'weekly_discount' => 10, // 10% off for 7+ days
            'available_from' => now()->toDateString(),
            'available_to' => now()->addMonth()->toDateString(),
            'admin_approve' => true,
            'status' => 'available',
        ]);

        // Book 7 days (diff is 6 + 1 = 7 days => 700 - 10% = 630)
        $this->actingAs($guest)->post(route('bookings.store', $apartment->id), [
            'apartment_id' => $apartment->id,
            'start_date' => now()->addDays(1)->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
        ]);

        $booking = Booking::first();
        $this->assertNotNull($booking);
        $this->assertEquals(630, $booking->total_price);
        $this->assertEquals(70, $booking->discount_amount);
    }

    public function test_wishlist_can_be_toggled()
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $apartment = Apartment::create([
            'user_id' => $owner->id,
            'title' => 'Wishlist Test Apt',
            'address' => 'Maadi',
            'price_per_night' => 80,
            'available_from' => now()->toDateString(),
            'available_to' => now()->addMonth()->toDateString(),
            'admin_approve' => true,
            'status' => 'available',
        ]);

        $res1 = $this->actingAs($user)->postJson(route('wishlists.toggle', $apartment->id));
        $res1->assertJson(['favorited' => true]);
        $this->assertTrue($apartment->isFavoritedBy($user));

        $res2 = $this->actingAs($user)->postJson(route('wishlists.toggle', $apartment->id));
        $res2->assertJson(['favorited' => false]);
        $this->assertFalse($apartment->isFavoritedBy($user));
    }

    public function test_expire_unpaid_bookings_command()
    {
        $owner = User::factory()->create();
        $guest = User::factory()->create();
        $apartment = Apartment::create([
            'user_id' => $owner->id,
            'title' => 'Test Apartment',
            'address' => 'Giza',
            'price_per_night' => 50,
            'available_from' => now()->toDateString(),
            'available_to' => now()->addMonth()->toDateString(),
            'admin_approve' => true,
            'status' => 'available',
        ]);

        // Booking with past deadline
        $booking = Booking::create([
            'user_id' => $guest->id,
            'apartment_id' => $apartment->id,
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'total_price' => 150,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_deadline' => now()->subHour(),
        ]);

        $this->artisan('bookings:expire-unpaid')
            ->assertSuccessful();

        $booking->refresh();
        $this->assertEquals('expired', $booking->status);
    }
}
