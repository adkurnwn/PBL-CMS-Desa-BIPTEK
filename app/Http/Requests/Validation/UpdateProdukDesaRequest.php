<?php

namespace App\Http\Requests\Validation;

use App\Models\ProdukDesa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateProdukDesaRequest extends FormRequest
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
            'product_name' => 'required|string|max:255',
            'price' => 'required|decimal:0',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'x' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'alamat_penjual' => 'required|string|max:255',
            'nama_penjual' => 'required|string|max:255',
            'kontak_penjual' => 'required|string|max:20',
            'description' => 'required',
            'images' => 'sometimes|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10000',
            'removed_images' => 'sometimes|array',
            'removed_images.*' => 'exists:gambar_produk_desa,id',
            'status' => 'required|boolean',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $produkDesaId = $this->route('produkDesa')->id ?? null;

            if ($produkDesaId && $this->hasFile('images')) {
                $produkDesa = ProdukDesa::findOrFail($produkDesaId);
                $existingImagesCount = $produkDesa->images()->count();
                $newImagesCount = count($this->file('images'));
                $removedImagesCount = $this->input('removed_images') ? count($this->input('removed_images')) : 0;

                $totalImagesAfterUpdate = $existingImagesCount + $newImagesCount - $removedImagesCount;

                if ($totalImagesAfterUpdate > 3) {
                    $validator->errors()->add(
                        'images',
                        'The product cannot have more than 3 images. Please remove some existing images before adding new ones.'
                    );
                }
            }
        });
    }
}
