<?php

namespace App\Http\Requests\Validation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
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
            'app_name' => 'required|string|max:255',
            'app_timezone' => 'required|string|timezone',
            'app_url' => 'required|url',
            'db_connection' => 'nullable|string|in:mysql,pgsql,sqlite,sqlsrv',
            'db_host' => 'nullable|string|max:255',
            'db_port' => 'nullable|numeric',
            'db_database' => 'nullable|string|max:255',
            'db_username' => 'nullable|string|max:255',
            'db_password' => 'nullable|string',
            'mail_mailer' => 'nullable|string|in:smtp,sendmail,mailgun,ses,postmark,log,array',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|numeric',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string|in:tls,ssl,null',
            'mail_from_address' => 'nullable|email',
            'mail_from_name' => 'nullable|string|max:255',
            'favicon' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,gif,ico',
            'primary_color' => 'nullable|string',
            'secondary_color' => 'nullable|string',
        ];
    }
}
