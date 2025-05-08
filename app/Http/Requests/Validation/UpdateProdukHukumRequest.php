<?php

namespace App\Http\Requests\Validation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProdukHukumRequest extends FormRequest
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
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'date' => 'required|date',
            'is_moderate' => 'sometimes|boolean',
        ];

        // If a new file is being uploaded, link is optional
        if ($this->hasFile('file')) {
            $rules['file'] = 'file|mimes:pdf,doc,docx';
            $rules['link'] = 'nullable|url';
        } else {
            // If no file is uploaded and the product doesn't have a file, link is required
            $produkHukum = $this->route('produk_hukum');

            if (!$produkHukum || !$produkHukum->file) {
                $rules['link'] = 'required|url';
            } else {
                $rules['link'] = 'nullable|url';
            }
        }

        // Add validation for category selection
        if ($this->filled('category.new_category')) {
            $rules['category.new_category'] = 'required|string|max:255';
        } else if ($this->filled('category.edit_category_id') && $this->filled('category.new_category_name')) {
            $rules['category.edit_category_id'] = 'required|exists:categories,id';
            $rules['category.new_category_name'] = 'required|string|max:255';
        } else {
            $rules['category.category_id'] = 'required|exists:categories,id';
        }

        if ($this->filled('category.deleting_category_id')) {
            $rules['category.deleting_category_id.*'] = 'required|exists:categories,id';
        }

        return $rules;
    }
}
