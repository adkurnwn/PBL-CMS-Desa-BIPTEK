<?php

namespace App\Http\Requests\Validation;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'images' => 'required|array|max:5',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:10000',
            'alts.*' => 'nullable|string|max:255',
            'video' => 'nullable|mimetypes:video/mp4,video/ogg,video/webm|max:102400',
            'is_moderate' => 'required|boolean',
            'is_draft' => 'required|boolean',
        ];

        if ($this->filled('category.new_category')) {
            $rules['category.new_category'] = 'required|string|max:255';
        } else {
            $rules['category.category_id'] = 'required|exists:categories,id';
        }

        if ($this->filled('category.edit_category_id') && $this->filled('category.new_category_name')) {
            $rules['category.edit_category_id'] = 'required|exists:categories,id';
            $rules['category.new_category_name'] = 'required|string|max:255';
        }

        return $rules;
    }
}
