<?php

namespace App\Http\Requests\Validation;

use App\Models\Gallery;
use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGalleryRequest extends FormRequest
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
            'images' => 'sometimes|array',
            'images.*' => 'sometimes|image|mimes:jpg,jpeg,png|max:10000',
            'alts.*' => 'required_with:images.*|string|max:255',
            'removed_images' => 'sometimes|array',
            'removed_images.*' => 'exists:images,id'
        ];

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

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $galleryId = $this->route('gallery')->id ?? null;

            if ($galleryId && $this->hasFile('images')) {
                $gallery = Gallery::findOrFail($galleryId);
                $existingImagesCount = $gallery->images()->count();
                $newImagesCount = count($this->file('images'));
                $removedImagesCount = $this->input('removed_images') ? count($this->input('removed_images')) : 0;

                $totalImagesAfterUpdate = $existingImagesCount + $newImagesCount - $removedImagesCount;

                if ($totalImagesAfterUpdate > 5) {
                    $validator->errors()->add(
                        'images',
                        'The gallery cannot have more than 25 images. Please remove some existing images before adding new ones.'
                    );
                }
            }
        });
    }
}
