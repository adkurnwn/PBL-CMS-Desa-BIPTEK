<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Event;
use App\Helpers\SlugHelper;
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
        $data['slug'] = SlugHelper::generateUniqueSlug(Event::class, $request->title);
        $data['user_id'] = Auth::id();

        Event::create($data);

        $this->logHelper->logActivity('create', 'event', 'Menambahkan event ' . $request->title);
        return redirect()->route('dashboard.events.index')->with('success', 'Event Berhasil Ditambahkan');
    }

    public function edit($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        return Inertia::render('Dashboard/Events/Edit', [
            'event' => $event,
        ]);
    }

    public function update(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'date' => 'required',
            'time' => 'required',
            'location' => 'required',
        ];

        $request->validate($rules);

        $data = $request->all();
        if ($event->title !== $request->title) {
            $data['slug'] = SlugHelper::generateUniqueSlug(Event::class, $request->title);
        }

        $event->update($data);

        $this->logHelper->logActivity('update', 'event', 'Memperbarui event ' . $request->title);
        return redirect()->route('dashboard.events.index')->with('success', 'Event Berhasil Diperbarui');
    }
}
