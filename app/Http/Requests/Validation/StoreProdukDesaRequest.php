<?php

namespace App\Http\Requests\Validation;

use Illuminate\Foundation\Http\FormRequest;

class StoreProdukDesaRequest extends FormRequest
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
            'product_name' => 'required|string|max:255',
            'price' => 'required|decimal:0',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'x' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'alamat_penjual' => 'required|string|max:255',
            'nama_penjual' => 'required|string|max:255',
            'kontak_penjual' => 'required|string|max:20',
            'description' => 'required|string',
            'images' => 'required|array|max:3',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10000',
            'status' => 'required|boolean',
        ];
    }
}
