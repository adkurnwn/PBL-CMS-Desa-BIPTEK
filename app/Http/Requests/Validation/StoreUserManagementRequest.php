<?php

namespace App\Http\Requests\Validation;

use App\Models\User;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserManagementRequest extends FormRequest
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
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', Password::defaults()],
            'phone' => 'required|string|max:20',
            'photo_profile' => 'required|image|max:1024|mimes:jpg,png,jpeg',
        ];

        if ($this->filled('new_position')) {
            $rules['new_position'] = 'required|string|max:255';
            $rules['job_descriptions'] = 'required|array|min:1';
        } else {
            $rules['position_id'] = 'required|exists:positions,id';
        }

        return $rules;
    }
}
