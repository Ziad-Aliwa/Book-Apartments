<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'address',
        'price_per_night',
        'available_from',
        'available_to',
        'admin_approve',
        'status',
    ];

    protected $appends = ['cover_url', 'average_rating', 'reviews_count'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ApartmentImage::class);
    }

    public function coverImage(): HasOne
    {
        return $this->hasOne(ApartmentImage::class)->where('is_cover', true);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function getCoverUrlAttribute(): string
    {
        $cover = $this->images->firstWhere('is_cover', true) ?? $this->images->first();
        if ($cover) {
            return Storage::url($cover->image_path);
        }

        // Return a clean stylish placeholder SVG if no image uploaded
        return 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80';
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }
}
