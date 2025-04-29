<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Mail\FeedbackResponse;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class FeedbackController extends Controller
{
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

        return back()->with('success', 'Feedback telah ditanggapi dan email telah dikirim.');
    }

}
