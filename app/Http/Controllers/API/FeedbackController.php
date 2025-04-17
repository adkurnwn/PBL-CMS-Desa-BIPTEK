<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'kategori' => ['required', Rule::in(['Saran', 'Keluhan'])],
            'nama_user' => 'required|string|max:50',
            'email' => 'required|email|max:50',
        ]);

        $feedback = Feedback::create([
            'judul' => $validated['judul'],
            'isi' => $validated['isi'],
            'kategori' => $validated['kategori'],
            'nama_user' => $validated['nama_user'],
            'email' => $validated['email'],
            'status' => 'Menunggu'
        ]);

        return response()->json([
            'message' => 'Feedback berhasil dikirim',
            'feedback' => $feedback
        ], 201);
    }

    public function getKategori()
    {
        $kategori = ['Saran', 'Keluhan'];
        return response()->json($kategori);
    }
}
