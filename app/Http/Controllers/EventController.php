<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\TicketType;
use OpenApi\Attributes as OA;

class EventController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Event::with(['category', 'ticketTypes'])
            ->where('status', 'published')
            ->where('date_start', '>=', now());

        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('date_end', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('date_start', '<=', $request->date_to);
        }

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
        if (Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Nemate dozvolu za brisanje događaja.');
        }

        $event->delete();
        return redirect()->route('dashboard')->with('success', 'Događaj je uspešno obrisan!');
    }

    public function edit(Event $event)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
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

    // ==================== JSON API metode ====================

    #[OA\Get(
        path: "/api/events",
        operationId: "apiGetEvents",
        summary: "Preuzmi listu publikovanih događaja",
        description: "Vraća paginiranu listu svih publikovanih događaja koji još nisu prošli",
        tags: ["Events"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Uspešna operacija",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "current_page", type: "integer", example: 1),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "title", type: "string", example: "Rock Koncert"),
                                    new OA\Property(property: "description", type: "string", example: "Opis događaja"),
                                    new OA\Property(property: "location", type: "string", example: "Beogradska Arena"),
                                    new OA\Property(property: "date_start", type: "string", format: "date-time"),
                                    new OA\Property(property: "date_end", type: "string", format: "date-time"),
                                    new OA\Property(property: "status", type: "string", example: "published"),
                                    new OA\Property(property: "category_id", type: "integer", example: 1),
                                    new OA\Property(
                                        property: "category",
                                        type: "object",
                                        properties: [
                                            new OA\Property(property: "id", type: "integer", example: 1),
                                            new OA\Property(property: "name", type: "string", example: "Muzika"),
                                        ]
                                    ),
                                    new OA\Property(
                                        property: "ticket_types",
                                        type: "array",
                                        items: new OA\Items(
                                            properties: [
                                                new OA\Property(property: "id", type: "integer", example: 1),
                                                new OA\Property(property: "name", type: "string", example: "standard"),
                                                new OA\Property(property: "price", type: "number", example: 1000),
                                                new OA\Property(property: "capacity", type: "integer", example: 500),
                                                new OA\Property(property: "sold", type: "integer", example: 42),
                                            ]
                                        )
                                    ),
                                ]
                            )
                        ),
                        new OA\Property(property: "last_page", type: "integer", example: 3),
                        new OA\Property(property: "per_page", type: "integer", example: 9),
                        new OA\Property(property: "total", type: "integer", example: 25),
                    ]
                )
            ),
        ]
    )]
    public function apiIndex(Request $request)
    {
        $events = Event::with(['category', 'ticketTypes'])
            ->where('status', 'published')
            ->where('date_start', '>=', now())
            ->orderBy('date_start', 'asc')
            ->paginate(9);

        return response()->json($events);
    }

    #[OA\Get(
        path: "/api/events/{event}",
        operationId: "apiGetEvent",
        summary: "Preuzmi pojedinačni događaj",
        description: "Vraća detalje jednog događaja sa kategorijom i tipovima karata",
        tags: ["Events"],
        parameters: [
            new OA\Parameter(
                name: "event",
                in: "path",
                description: "ID događaja",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Uspešna operacija",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "title", type: "string", example: "Rock Koncert"),
                        new OA\Property(property: "description", type: "string", example: "Opis događaja"),
                        new OA\Property(property: "location", type: "string", example: "Beogradska Arena"),
                        new OA\Property(property: "date_start", type: "string", format: "date-time"),
                        new OA\Property(property: "date_end", type: "string", format: "date-time"),
                        new OA\Property(property: "status", type: "string", example: "published"),
                        new OA\Property(property: "category_id", type: "integer", example: 1),
                        new OA\Property(
                            property: "category",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Muzika"),
                            ]
                        ),
                        new OA\Property(
                            property: "ticket_types",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "name", type: "string", example: "standard"),
                                    new OA\Property(property: "price", type: "number", example: 1000),
                                    new OA\Property(property: "capacity", type: "integer", example: 500),
                                    new OA\Property(property: "sold", type: "integer", example: 42),
                                ]
                            )
                        ),
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Događaj nije pronađen"),
        ]
    )]
    public function apiShow(Event $event)
    {
        $event->load(['category', 'ticketTypes']);
        return response()->json($event);
    }

    #[OA\Post(
        path: "/api/events",
        operationId: "apiStoreEvent",
        summary: "Kreiraj novi događaj",
        description: "Kreira novi događaj (potrebna autentifikacija)",
        security: [["sanctum" => []]],
        tags: ["Events"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title", "description", "location", "date_start", "date_end", "category_id", "status"],
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Novi Koncert"),
                    new OA\Property(property: "description", type: "string", example: "Detaljan opis događaja"),
                    new OA\Property(property: "location", type: "string", example: "Kombank Dvorana"),
                    new OA\Property(property: "date_start", type: "string", format: "date-time", example: "2026-06-01T19:00:00Z"),
                    new OA\Property(property: "date_end", type: "string", format: "date-time", example: "2026-06-01T23:00:00Z"),
                    new OA\Property(property: "category_id", type: "integer", example: 1),
                    new OA\Property(property: "status", type: "string", enum: ["draft", "published", "cancelled"], example: "published"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Događaj uspešno kreiran",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 10),
                        new OA\Property(property: "title", type: "string"),
                        new OA\Property(property: "description", type: "string"),
                        new OA\Property(property: "location", type: "string"),
                        new OA\Property(property: "date_start", type: "string", format: "date-time"),
                        new OA\Property(property: "date_end", type: "string", format: "date-time"),
                        new OA\Property(property: "category_id", type: "integer"),
                        new OA\Property(property: "status", type: "string"),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Neautorizovan pristup"),
            new OA\Response(response: 422, description: "Validacione greške"),
        ]
    )]
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

    #[OA\Put(
        path: "/api/events/{event}",
        operationId: "apiUpdateEvent",
        summary: "Izmeni postojeći događaj",
        description: "Ažurira podatke događaja (potrebna autentifikacija)",
        security: [["sanctum" => []]],
        tags: ["Events"],
        parameters: [
            new OA\Parameter(
                name: "event",
                in: "path",
                description: "ID događaja",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title", "description", "location", "date_start", "date_end", "category_id", "status"],
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Izmenjeni Koncert"),
                    new OA\Property(property: "description", type: "string", example: "Novi opis"),
                    new OA\Property(property: "location", type: "string", example: "Štark Arena"),
                    new OA\Property(property: "date_start", type: "string", format: "date-time"),
                    new OA\Property(property: "date_end", type: "string", format: "date-time"),
                    new OA\Property(property: "category_id", type: "integer", example: 1),
                    new OA\Property(property: "status", type: "string", enum: ["draft", "published", "cancelled"], example: "published"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Događaj uspešno ažuriran",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "title", type: "string"),
                        new OA\Property(property: "description", type: "string"),
                        new OA\Property(property: "location", type: "string"),
                        new OA\Property(property: "date_start", type: "string", format: "date-time"),
                        new OA\Property(property: "date_end", type: "string", format: "date-time"),
                        new OA\Property(property: "category_id", type: "integer"),
                        new OA\Property(property: "status", type: "string"),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Neautorizovan pristup"),
            new OA\Response(response: 404, description: "Događaj nije pronađen"),
            new OA\Response(response: 422, description: "Validacione greške"),
        ]
    )]
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

    #[OA\Delete(
        path: "/api/events/{event}",
        operationId: "apiDeleteEvent",
        summary: "Obriši događaj",
        description: "Briše događaj iz baze (potrebna autentifikacija)",
        security: [["sanctum" => []]],
        tags: ["Events"],
        parameters: [
            new OA\Parameter(
                name: "event",
                in: "path",
                description: "ID događaja",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Događaj uspešno obrisan",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Event deleted"),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Neautorizovan pristup"),
            new OA\Response(response: 404, description: "Događaj nije pronađen"),
        ]
    )]
    public function apiDestroy(Event $event)
    {
        $event->delete();
        return response()->json(['message' => 'Event deleted'], 200);
    }
}
