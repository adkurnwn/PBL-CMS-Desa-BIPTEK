<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDanaDesaRequest extends FormRequest
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
            $rules['file'] = 'required|file|mimes:pdf,doc,docx';
            $rules['link'] = 'nullable|url';
        } else {
            $rules['link'] = 'required|url';
        }

        return $rules;
    }
}
