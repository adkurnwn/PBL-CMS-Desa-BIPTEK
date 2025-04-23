<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventEmails;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('date', 'asc')
            ->where('date', '>=', now())
            ->get();

        return response()->json($events);
    }

    public function setReminder(Request $request, Event $event)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $existingReminder = EventEmails::where('event_id', $event->id)
                ->where('email', $request->email)
                ->exists();

            if ($existingReminder) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already registered for this event'
                ], 422);
            }

            EventEmails::create([
                'event_id' => $event->id,
                'email' => $request->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully registered for event reminder'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while registering for the event'
            ], 500);
        }
    }
}