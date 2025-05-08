<?php

namespace App\Http\Requests\Validation;

use App\Models\User;
use App\Models\Position;
use Illuminate\Validation\Validator;
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
            'position_id' => 'required|exists:positions,id',
        ];

        if ($this->filled('password')) {
            $rules['password'] = ['required', Password::defaults()];
        }

        if ($this->hasFile('photo_profile')) {
            $rules['photo_profile'] = 'image|max:1024|mimes:jpg,png,jpeg';
        }

        return $rules;
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            // Check if the position is Kepala Desa
            $isKepalaDesa = false;

            if ($this->filled('position_id')) {
                $position = Position::find($this->position_id);
                if ($position && $position->name === 'Kepala Desa') {
                    $isKepalaDesa = true;
                }
            }

            // Check for existing active Kepala Desa
            if ($isKepalaDesa) {
                $existingKepalaDesa = User::whereHas('roles', function ($query) {
                    $query->where('name', 'Kepala Desa');
                })
                    ->where('status', true)
                    ->where('id', '!=', $this->user->id)
                    ->first();

                if ($existingKepalaDesa) {
                    $validator->errors()->add(
                        'position_id',
                        'There is already an active Kepala Desa'
                    );
                }
            }
        });
    }
}
