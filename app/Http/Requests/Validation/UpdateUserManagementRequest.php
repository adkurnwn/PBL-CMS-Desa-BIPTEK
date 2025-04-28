<?php

namespace App\Http\Requests\Validation;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserManagementRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $this->user->id,
            'phone' => 'required|string|max:20',
        ];

        if ($this->filled('new_position')) {
            $rules['new_position'] = 'required|string|max:255';
            $rules['job_descriptions'] = 'required|array|min:1';
        } else {
            $rules['position_id'] = 'required|exists:positions,id';
        }

        if ($this->filled('password')) {
            $rules['password'] = ['required', Password::defaults()];
        }

        if ($this->hasFile('photo_profile')) {
            $rules['photo_profile'] = 'image|max:1024|mimes:jpg,png,jpeg';
        }

        return $rules;
    }
}
