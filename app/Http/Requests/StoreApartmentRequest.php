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
            'price_per_night' => 'required|numeric|min:1',
            'available_from' => 'required|date|after_or_equal:today',
            'available_to' => 'required|date|after_or_equal:available_from',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'cover_index' => 'nullable|integer|min:0',
        ];
    }
}
