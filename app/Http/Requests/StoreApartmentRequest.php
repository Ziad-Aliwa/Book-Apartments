<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'price_per_night' => 'required|numeric|min:1',
            'weekly_discount' => 'nullable|numeric|min:0|max:100',
            'monthly_discount' => 'nullable|numeric|min:0|max:100',
            'bedrooms' => 'nullable|integer|min:0|max:50',
            'bathrooms' => 'nullable|integer|min:0|max:50',
            'max_guests' => 'nullable|integer|min:1|max:100',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:50',
            'available_from' => 'required|date|after_or_equal:today',
            'available_to' => 'required|date|after_or_equal:available_from',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'cover_index' => 'nullable|integer|min:0',
        ];
    }
}
