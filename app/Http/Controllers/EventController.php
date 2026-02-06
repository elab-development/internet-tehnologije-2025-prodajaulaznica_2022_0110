<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;

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
}
