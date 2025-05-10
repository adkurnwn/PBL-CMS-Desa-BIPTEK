<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Helpers\LogActivityHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    protected $logHelper;

    public function __construct()
    {
        $this->logHelper = new LogActivityHelper();
    }

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
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'date' => 'required',
            'time' => 'required',
            'location' => 'required',
        ];

        $request->validate($rules);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        Event::create($data);

        $this->logHelper->logActivity('create', 'event', 'Menambahkan event ' . $request->title);
        return redirect()->route('dashboard.events.index')->with('success', 'Event Berhasil Ditambahkan');
    }

    public function edit(Event $event)
    {
        return Inertia::render('Dashboard/Events/Edit', [
            'event' => $event,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'date' => 'required',
            'time' => 'required',
            'location' => 'required',
        ];

        $request->validate($rules);

        $data = $request->all();

        $event->update($data);

        $this->logHelper->logActivity('update', 'event', 'Memperbarui event ' . $request->title);
        return redirect()->route('dashboard.events.index')->with('success', 'Event Berhasil Diperbarui');
    }
}
