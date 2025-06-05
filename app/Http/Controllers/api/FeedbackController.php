<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        // First validate the max number of files
        if ($request->hasFile('bukti') && count($request->file('bukti')) > 3) {
            return response()->json([
                'message' => 'Maksimal 3 file yang diperbolehkan.',
                'errors' => ['bukti' => ['Maksimal 3 file yang diperbolehkan.']]
            ], 422);
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'kategori' => ['required', Rule::in(['Saran', 'Keluhan'])],
            'nama_user' => 'required|string|max:50',
            'email' => 'required|email|max:50',
            'bukti' => 'nullable|array|max:3', // Add array validation with max:3
            'bukti.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:1024',
            'captcha' => 'required|captcha_api:' . $request->code . ',default'
        ], [
            'captcha.captcha_api' => 'Kode captcha tidak valid'
        ]);

        $data = [
            'judul' => $validated['judul'],
            'isi' => $validated['isi'],
            'kategori' => $validated['kategori'],
            'nama_user' => $validated['nama_user'],
            'email' => $validated['email'],
            'status' => 'Menunggu'
        ];

        // Handle multiple file uploads
        if ($request->hasFile('bukti')) {
            $paths = [];
            foreach ($request->file('bukti') as $file) {
                $hash = md5(uniqid() . time());
                $filename = $hash . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('bukti-images', $filename, 'public');
                $paths[] = $path;
            }
            $data['bukti'] = json_encode($paths);
        }

        $feedback = Feedback::create($data);

        return response()->json([
            'message' => 'Feedback berhasil dikirim',
            //'feedback' => $feedback
        ], 201);
    }

    public function getKategori()
    {
        $kategori = ['Saran', 'Keluhan'];
        return response()->json($kategori);
    }
}
