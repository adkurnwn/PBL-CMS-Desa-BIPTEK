<?php

namespace App\Http\Requests\Validation;

use App\Models\News;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateNewsRequest extends FormRequest
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
            'images' => 'sometimes|array',
            'images.*' => 'sometimes|image|mimes:jpg,jpeg,png|max:10000',
            'alts.*' => 'nullable|string|max:255',
            'removed_images' => 'sometimes|array',
            'removed_images.*' => 'exists:images,id',
            'video' => 'nullable|mimetypes:video/mp4,video/ogg,video/webm|max:102400',
            'is_moderate' => 'required|boolean',
            'is_draft' => 'required|boolean',
            'updated_existing_images' => 'sometimes|array',
            'updated_existing_images.*.id' => 'required|exists:images,id',
            'updated_existing_images.*.alt' => 'nullable|string|max:255',
        ];

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

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $newsId = $this->route('news')->id ?? null;

            if ($newsId) {
                $news = News::findOrFail($newsId);
                $existingImagesCount = $news->images()->count();
                $newImagesCount = $this->hasFile('images') ? count($this->file('images')) : 0;
                $removedImagesCount = $this->input('removed_images') ? count($this->input('removed_images')) : 0;

                $totalImagesAfterUpdate = $existingImagesCount + $newImagesCount - $removedImagesCount;

                if ($newImagesCount > 0 && $totalImagesAfterUpdate > 5) {
                    $validator->errors()->add(
                        'images',
                        'The news cannot have more than 5 images. Please remove some existing images before adding new ones.'
                    );
                }

                if ($totalImagesAfterUpdate < 1) {
                    $validator->errors()->add(
                        'images',
                        'The news must have at least 1 image.'
                    );
                }
            }
        });
    }
}
