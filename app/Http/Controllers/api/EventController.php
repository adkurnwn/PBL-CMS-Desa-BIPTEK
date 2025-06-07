<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Event\EventPreview;
use App\Models\Event;
use App\Models\EventEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EventController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $sort = $request->query('sort');

            $query = Event::query();

            // Search by title or content
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%');
                });
            }

            if ($sort === 'terlama') {
                $query->oldest();
            } else {
                $query->latest();
            }

            $events = $query->latest()->paginate(12);

            if ($events->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No events found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => EventPreview::collection($events),
                'meta' => [
                    'current_page' => $events->currentPage(),
                    'last_page' => $events->lastPage(),
                    'per_page' => $events->perPage(),
                    'total' => $events->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => app()->environment('production') ? null : $e->getMessage()
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

    public function preview()
    {
        try {
            $events = Cache::remember('upcoming_events', now()->addDay(), function () {
                return
                    Event::orderBy('date', 'asc')
                    ->where(function ($query) {
                        $now = now();
                        $query->where('date', '>', $now->format('Y-m-d'))
                            ->orWhere(function ($q) use ($now) {
                                $q->where('date', '=', $now->format('Y-m-d'))
                                    ->where('time', '>=', $now->format('H:i:s'));
                            });
                    })->take(3)->get();
            });

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
}
