<?php

namespace App\Console\Commands;

use App\Mail\EventReminder;
use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SendEventReminders extends Command
{
    protected $signature = 'app:send-event-reminders';
    protected $description = 'Send reminder emails for upcoming events';

    public function handle()
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        // Get events happening today or tomorrow that have unsent email reminders
        $events = Event::where(function($query) use ($today, $tomorrow) {
                // Events happening tomorrow (standard d-1 reminders)
                $query->whereDate('date', $tomorrow->toDateString())
                    // OR events happening today (late registrations)
                    ->orWhereDate('date', $today->toDateString());
            })
            ->with(['eventEmails' => function($query) {
                $query->where('is_sent', false);
            }])
            ->get();

        $this->info("Processing reminders for " . $events->count() . " events");

        foreach ($events as $event) {
            if ($event->eventEmails->isEmpty()) {
                continue;
            }

            $eventDate = Carbon::parse($event->date);
            $reminderType = $eventDate->isToday() ? "same-day" : "day-before";
            
            foreach ($event->eventEmails as $eventEmail) {
                try {
                    Mail::to($eventEmail->email)->send(new EventReminder($event));
                    
                    DB::table('event_emails')
                        ->where('id', $eventEmail->id)
                        ->update(['is_sent' => true]);
                    
                    $this->info("Sent {$reminderType} reminder email to {$eventEmail->email} for event {$event->title}");
                } catch (\Exception $e) {
                    $this->error("Failed to send email to {$eventEmail->email} for event {$event->title}: {$e->getMessage()}");
                }
            }
        }
    }
}