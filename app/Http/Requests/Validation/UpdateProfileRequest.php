<?php

namespace App\Http\Requests\Validation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|lowercase|email|max:255',
            'phone' => 'required|string|max:20',
            'provinsi' => 'required|string|max:50',
            'kabupaten' => 'required|string|max:50',
            'kecamatan' => 'required|string|max:50',
            'kelurahan' => 'required|string|max:50',
            'logo' => 'nullable|image|max:10000|mimes:png',
            'lembaga' => 'nullable|array',
            'lembaga.*' => 'required|string|max:255',
            'fasilitas' => 'nullable|array',
            'fasilitas.*' => 'required|string|max:255',
            'history' => 'required|string',
            'vision' => 'required|string',
            'mission' => 'required|string',
            'organization_image' => 'nullable|image|max:10000|mimes:jpg,png,jpeg',
            'primary_color' => 'required|in:#000000,#ffffff',
            'secondary_color' => 'required|in:#000000,#ffffff',
            'hero_images.*' => 'sometimes|image|max:10000|mimes:jpg,png,jpeg',
            'hero_image_1' => 'nullable|image|max:10000|mimes:jpg,png,jpeg',
            'hero_image_2' => 'nullable|image|max:10000|mimes:jpg,png,jpeg',
            'hero_image_3' => 'nullable|image|max:10000|mimes:jpg,png,jpeg',
        ];
    }
}
