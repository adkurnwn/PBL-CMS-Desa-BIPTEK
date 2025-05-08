<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Feedback;
use App\Models\Profile;

class FeedbackResponse extends Mailable
{
    use Queueable, SerializesModels;

    public $feedback;

    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }

    public function build()
    {
        $profile = Profile::first();
        return $this->subject('Tanggapan ' . $this->feedback->kategori . ' ' . $this->feedback->created_at->format('d F Y H:i'))
                    ->view('emails.feedback-response')->with([
                        'feedback' => $this->feedback,
                        'profile' => $profile
                    ]);
    }
}
