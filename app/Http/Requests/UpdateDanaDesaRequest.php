<?php

namespace App\Http\Requests;

use App\Models\DanaDesa;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDanaDesaRequest extends FormRequest
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
            'date' => 'required|date',
        ];

        if ($this->hasFile('file')) {
            $rules['file'] = 'file|mimes:pdf,doc,docx';
            $rules['link'] = 'nullable|url';
        } else {
            $slug = $this->route('slug');
            $danaDesa = DanaDesa::where('slug', $slug)->first();

            if (!$danaDesa || !$danaDesa->file) {
                $rules['link'] = 'required|url';
            } else {
                $rules['link'] = 'nullable|url';
            }
        }

        return $rules;
    }
}
