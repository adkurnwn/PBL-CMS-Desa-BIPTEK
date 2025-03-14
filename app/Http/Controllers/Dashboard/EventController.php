<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Events/Index', [
            'events' => Event::all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/Events/Create');
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'date' => 'required',
            'time' => 'required',
            'location' => 'required',
        ]);

        // Create the event
        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'location' => $request->location,
        ]);

        // Redirect to the events index page
        return redirect()->route('dashboard.events');
    }

    public function edit(Event $event)
    {
        return Inertia::render('Dashboard/Events/Edit', [
            'event' => $event,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        // Validate the request
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'date' => 'required',
            'time' => 'required',
            'location' => 'required',
        ]);

        // Update the event
        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'location' => $request->location,
        ]);

        // Redirect to the events index page
        return redirect()->route('dashboard.events');
    }
}
