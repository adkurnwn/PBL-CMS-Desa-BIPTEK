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

    public function index()
    {
        return Inertia::render('Dashboard/Feedbacks/Index', [
            'feedbacks' => Feedback::orderBy('created_at', 'desc')->paginate(10)
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
