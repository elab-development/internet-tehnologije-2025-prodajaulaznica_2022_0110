<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\TicketType;

class EventController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Event::with(['category', 'ticketTypes'])
            ->where('status', 'published')
            ->where('date_start', '>=', now());

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Category filter
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Date range filter
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('date_end', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('date_start', '<=', $request->date_to);
        }

        // Sort by date_start (najbliži događaji prvo)
        $events = $query->orderBy('date_start', 'asc')->paginate(9);

        $categories = Category::all();

        return Inertia::render('Dashboard', [
            'events' => $events,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'date_from', 'date_to']),
        ]);
    }

    public function destroy(Event $event)
    {
        // Proveri da li je korisnik admin
        if (Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Nemate dozvolu za brisanje događaja.');
        }

        $event->delete();

        return redirect()->route('dashboard')->with('success', 'Događaj je uspešno obrisan!');
    }
    public function edit(Event $event)
    {
        // Proveri da li je admin
        if (!Auth::check()|| Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Nemate dozvolu.');
        }

        $categories = Category::all();

        return Inertia::render('Events/Edit', [
            'event' => $event->load('category'),
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        // Proveri da li je admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Nemate dozvolu.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published,cancelled',
        ]);

        $event->update($validated);

        return redirect()->route('dashboard')->with('success', 'Događaj je uspešno izmenjen!');
    }
    public function show(Event $event)
    {
        $event->load(['category', 'ticketTypes']);

        return Inertia::render('Events/Show', [
            'event' => $event,
        ]);
    }
    public function create()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'moderator'])) {
            return redirect()->route('dashboard')->with('error', 'Nemate dozvolu.');
        }

        $categories = Category::all();

        return Inertia::render('Events/Create', [
            'categories' => $categories,
        ]);
    }
    public function store(Request $request)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'moderator'])) {
            return redirect()->route('dashboard')->with('error', 'Nemate dozvolu.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published,cancelled',
        ]);

        $event = Event::create($validated);

        TicketType::create([
            'event_id' => $event->id,
            'name' => 'standard',
            'price' => 1000,
            'capacity' => 500,
            'sold' => 0,
        ]);

        TicketType::create([
            'event_id' => $event->id,
            'name' => 'premium',
            'price' => 3000,
            'capacity' => 200,
            'sold' => 0,
        ]);

        TicketType::create([
            'event_id' => $event->id,
            'name' => 'vip',
            'price' => 8000,
            'capacity' => 50,
            'sold' => 0,
        ]);

        return redirect()->route('dashboard')->with('success', 'Događaj je uspešno kreiran!');
    }
    public function updateTicketPrice(Request $request, TicketType $ticketType)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Nemate dozvolu.');
        }

        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
        ]);

        $ticketType->update($validated);

        return redirect()->back()->with('success', 'Cena je uspešno izmenjena!');
    }

    // JSON API metode
    public function apiIndex(Request $request)
    {
        $events = Event::with(['category', 'ticketTypes'])
            ->where('status', 'published')
            ->where('date_start', '>=', now())
            ->orderBy('date_start', 'asc')
            ->paginate(9);

        return response()->json($events);
    }

    public function apiShow(Event $event)
    {
        $event->load(['category', 'ticketTypes']);
        return response()->json($event);
    }

    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published,cancelled',
        ]);

        $event = Event::create($validated);
        return response()->json($event, 201);
    }

    public function apiUpdate(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published,cancelled',
        ]);

        $event->update($validated);
        return response()->json($event);
    }

    public function apiDestroy(Event $event)
    {
        $event->delete();
        return response()->json(['message' => 'Event deleted'], 200);
    }
}
