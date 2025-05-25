<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventEmails;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        try {
            $events = Event::orderBy('date', 'asc')
                ->where(function ($query) {
                    $now = now();
                    $query->where('date', '>', $now->format('Y-m-d'))
                        ->orWhere(function ($q) use ($now) {
                            $q->where('date', '=', $now->format('Y-m-d'))
                                ->where('time', '>=', $now->format('H:i:s'));
                        });
                })
                ->get();

            if ($events->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No events found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $events
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function setReminder(Request $request, Event $event)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            // Check if event is today
            if ($event->date === now()->format('Y-m-d')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event tidak dapat diingatkan pada hari yang sama!'
                ], 422);
            }

            $existingReminder = EventEmails::where('event_id', $event->id)
                ->where('email', $request->email)
                ->exists();

            if ($existingReminder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah terdaftar di pengingat event ini!'
                ], 422);
            }

            EventEmails::create([
                'event_id' => $event->id,
                'email' => $request->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mendaftar untuk pengingat event!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mendaftar pengingat event!'
            ], 500);
        }
    }
}
