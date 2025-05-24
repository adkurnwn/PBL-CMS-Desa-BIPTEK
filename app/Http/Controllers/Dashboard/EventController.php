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

    public function index(Request $request)
    {
        // Get the search term from request
        $search = $request->input('search');
        
        // Get the sort parameters from request (default to 'title' and 'asc')
        $sort = $request->input('sort', 'title');
        $direction = $request->input('direction', 'asc');
        
        // Process filters if any
        $filters = $request->input('filters', []);
        
        // Build the query
        $query = Event::query();
        
        // Apply search if provided
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        // Apply filters if any
        if (!empty($filters)) {
            // Example: Filter by date range
            if (isset($filters['date_range'])) {
                $dateRange = $filters['date_range'];
                if ($dateRange === 'future') {
                    $query->where('date', '>=', now()->format('Y-m-d'));
                } elseif ($dateRange === 'past') {
                    $query->where('date', '<', now()->format('Y-m-d'));
                } elseif ($dateRange === 'today') {
                    $query->whereDate('date', now()->format('Y-m-d'));
                }
            }
            
            // Example: Filter by location
            if (isset($filters['location']) && $filters['location']) {
                $query->where('location', $filters['location']);
            }
        }
        
        // Apply sorting
        $query->orderBy($sort, $direction);
        
        // Get paginated results (10 items per page)
        $events = $query->paginate(25)->withQueryString();
        
        return Inertia::render('Dashboard/Events/Index', [
            'events' => $events,
            'search' => $search,
            'filters' => $filters,
            'sort' => $sort,
            'direction' => $direction,
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

    public function show($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        return Inertia::render('Dashboard/Events/Show', [
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
        $data['slug'] = SlugHelper::generateUniqueSlug(Event::class, $request->title);

        $event->update($data);

        $this->logHelper->logActivity('update', 'event', 'Memperbarui event ' . $request->title);
        return redirect()->route('dashboard.events.index')->with('success', 'Event Berhasil Diperbarui');
    }
}
