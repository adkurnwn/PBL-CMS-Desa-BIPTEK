<?php

namespace App\Http\Requests\Validation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDemografiRequest extends FormRequest
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
        $year = $this->route('slug'); // Get the current year from the route

        return [
            'penduduk_pria' => 'required',
            'penduduk_wanita' => 'required',
            'tahun' => [
                'required',
                Rule::unique('demografi', 'tahun')->ignore($year, 'tahun')
            ],
            'jumlah_peserta_bpjs' => 'required',
            'jumlah_pindah_keluar' => 'required',
            'jumlah_pendatang' => 'required',
            'tamat_pt' => 'required',
            'tamat_sma' => 'required',
            'tamat_smp' => 'required',
            'tamat_sd' => 'required',
            'jumlah_lansia' => 'required',
            'jumlah_dewasa' => 'required',
            'jumlah_remaja' => 'required',
            'jumlah_anak' => 'required',
            'jumlah_penduduk' => 'required',
            'jumlah_keluarga' => 'required',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'tahun.unique' => 'Data demografi untuk tahun ini sudah ada.',
        ];
    }
}
