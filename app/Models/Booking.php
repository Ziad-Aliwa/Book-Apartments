<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'booking_code',
        'user_id',
        'apartment_id',
        'start_date',
        'end_date',
        'total_price', 
        'discount_amount',
        'paid_amount',
        'status',
        'payment_status',
        'payment_deadline',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_price' => 'float',
        'discount_amount' => 'float',
        'paid_amount' => 'float',
        'payment_deadline' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            if (empty($booking->booking_code)) {
                $booking->booking_code = 'BK-' . strtoupper(Str::random(8));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payments::class, 'booking_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
