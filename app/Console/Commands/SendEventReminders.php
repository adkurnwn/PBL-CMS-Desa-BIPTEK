<?php

namespace App\Console\Commands;

use App\Mail\EventReminder;
use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendEventReminders extends Command
{
    protected $signature = 'app:send-event-reminders';
    protected $description = 'Send reminder emails one day before events';

    public function handle()
    {
        $tomorrow = Carbon::tomorrow();

        $events = Event::whereDate('date', $tomorrow->toDateString())
                      ->whereTime('time', Carbon::now()->format('H:i'))
                      ->with(['eventEmails' => function($query) {
                          $query->where('is_sent', false);
                      }])
                      ->get();

        foreach ($events as $event) {
            foreach ($event->eventEmails as $eventEmail) {
                try {
                    Mail::to($eventEmail->email)->send(new EventReminder($event));
                    $eventEmail->update(['is_sent' => true]);
                    $this->info("Sent reminder email to {$eventEmail->email} for event {$event->title}");
                } catch (\Exception $e) {
                    $this->error("Failed to send email to {$eventEmail->email} for event {$event->title}");
                }
            }
        }
    }
}