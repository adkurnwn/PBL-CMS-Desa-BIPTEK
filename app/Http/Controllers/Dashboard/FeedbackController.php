<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Feedback;
use Illuminate\Http\Request;
use App\Mail\FeedbackResponse;
use App\Helpers\LogActivityHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class FeedbackController extends Controller
{
    protected $logHelper;

    public function __construct()
    {
        $this->logHelper = new LogActivityHelper();
    }

    public function index(Request $request)
    {
        $query = Feedback::query();

        // Handle search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('nama_user', 'like', "%{$search}%")
                  ->orWhere('isi', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Handle kategori filter - default to 'Keluhan' if not specified
        $kategori = $request->get('kategori', 'Keluhan');
        if ($kategori && in_array($kategori, ['Keluhan', 'Saran'])) {
            $query->where('kategori', $kategori);
        }

        // Handle status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Handle sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSortFields = ['judul', 'nama_user', 'created_at'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return Inertia::render('Dashboard/Feedbacks/Index', [
            'feedbacks' => $query->paginate(25)->withQueryString(),
            'search' => $request->search ?? '',
            'filters' => [
                'kategori' => $kategori,
                'status' => $request->status ?? '',
            ],
            'sort' => $sortField,
            'direction' => $sortDirection,
        ]);
    }

    public function show(Feedback $feedback)
    {
        return Inertia::render('Dashboard/Feedbacks/Show', [
            'feedback' => $feedback
        ]);
    }

    public function respond(Request $request, Feedback $feedback)
    {
        $request->validate([
            'tanggapan' => 'required|string'
        ]);

        $feedback->update([
            'tanggapan' => $request->tanggapan,
            'status' => 'Ditanggapi'
        ]);

        // Send email to user
        try {
            Mail::to($feedback->email)->send(new FeedbackResponse($feedback));
        } catch (\Exception $e) {
            return back()->with('error', 'Feedback telah ditanggapi tetapi gagal mengirim email.');
        }

        $this->logHelper->logActivity('update', 'feedback', 'Menanggapi feedback ' . $feedback->name);
        return back()->with('success', 'Feedback telah ditanggapi dan email telah dikirim.');
    }
}
