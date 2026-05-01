<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // BASIC
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',

            // COLORS (VARIANTS)
            'colors' => 'required|array|min:1',
            'colors.*.name' => 'required|string|max:50',
            'colors.*.price' => 'required|numeric|min:0',

            // GENERAL IMAGES
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',

            // COLOR IMAGES
            'color_images' => 'nullable|array',
            'color_images.*' => 'array',
            'color_images.*.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',

            // VIDEO
            'video' => 'nullable|file|mimetypes:video/mp4,video/avi|max:35000',

            // SIZES
            'sizes' => 'nullable|array',
            'sizes.*' => 'exists:sizes,id',

            // DETAILS
            'details' => 'nullable|array',
            'details.*.key' => 'required|string|max:100',
            'details.*.value' => 'required|string|max:255',

            // SIZE GUIDE (JSON)
            'size_guide' => 'nullable|array',
            'size_guide.columns' => 'required_with:size_guide|array',
            'size_guide.columns.*' => 'string',

            'size_guide.rows' => 'required_with:size_guide|array',
            'size_guide.rows.*' => 'array',

        ];
    }
}
