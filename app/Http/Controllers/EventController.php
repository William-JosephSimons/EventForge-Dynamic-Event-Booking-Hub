<?php

namespace App\Http\Controllers;

use App\Models\CategoryEvent;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    /**
     * @return: a listing of events.
     */
    public function index()
    {
        $events = Event::query()
            ->where('date_time', '>', now())
            ->orderBy('date_time', 'asc')
            ->paginate(8);

        // ensures that on initial load before a category is chosen that the paginator only injects the partial index
        $events->withPath(route('events.filter', 'all'));

        return view('events.index')
            ->with('events', $events)
            ->with('categories', Category::all());
    }

    /**
     * @return: the form for creating a new event
     */
    public function create()
    {
        return view('events.create')->with('categories', Category::all());
    }

    /**
     * Store a newly created event in the db.
     * @param Request $request: the post request with the input values
     * @return: a redirect back to the new event
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'date_time' => ['required', 'date', 'after:now'],
            'location' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1', 'max:1000'],
            'categories' => ['nullable', 'array'], // an event can have no categories
            'categories.*' => ['exists:categories,id'], // ensure every category chosen exists within the db
        ]);

        // remove categories and store event in the db
        $event_data = $validated;
        unset($event_data['categories']);

        // create event through user to store with organisers id
        $event = Auth::user()->events()->create($event_data);

        // add categories relationship back
        if (array_key_exists('categories', $validated)) {
            foreach ($validated['categories'] as $category) {
                CategoryEvent::create([
                    'event_id' => $event->id,
                    'category_id' => $category,
                ]);
            }
        }

        return redirect()
            ->route('events.show', $event->id)
            ->with('success', 'Event created successfully!');
    }

    /**
     * @param string $id: The id of the event within the db
     * @return: a view that displays a specific event based off its id
     */
    public function show(string $id)
    {
        $event = Event::where('id', $id)->firstOrFail();

        $hasBooked = false;
        if (Auth::check()) {
            $hasBooked = Auth::user()
                ->bookedEvents()
                ->where('event_id', $event->id)
                ->exists();
        }


        // FETCH RELATED EVENTS 
        $categoryIds = $event->categories->pluck('id');
        $relatedEvents = Event::query()
            ->where('id', '!=', $event->id)
            ->where('date_time', '>', now())
            // access the category table and grab events where they share categories with the current event
            ->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })
            // show 3 random events each time for variety 
            ->inRandomOrder()
            ->limit(3)
            ->get();


        return view('events.show')
            ->with('event', $event)
            ->with('hasBooked', $hasBooked)
            ->with('relatedEvents', $relatedEvents);
    }

    /**
     * @param string $id: The id of the event within the db
     * @return: the form for editing an event
     */
    public function edit(string $id)
    {
        $event = Event::where('id', $id)->firstOrFail();

        if (Auth::id() !== $event->user_id) {
            abort(403, 'UNAUTHORIZED ACTION');
        }

        return view('events.edit')
            ->with('event', $event)
            ->with('categories', Category::all());
    }

    /**
     * Updates the the specified event within the db.
     * @param Request $request: the put request with the input values.
     * @param string $id: id of the event within the db.
     * @return: a redirect back to the updated event
     */
    public function update(Request $request, string $id)
    {
        $event = Event::where('id', $id)->firstOrFail();

        if (Auth::id() !== $event->user_id) {
            abort(403, 'UNAUTHORIZED ACTION');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'date_time' => ['required', 'date', 'after:now'],
            'location' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1', 'max:1000'],
            'categories' => ['nullable', 'array'], // an event can have no categories
            'categories.*' => ['exists:categories,id'], // ensure every category chosen exists within the db
        ]);

        // remove categories and update event in the db
        $event_data = $validated;
        unset($event_data['categories']);

        $event->update($event_data);

        // update pivot table with new category relationships
        if (array_key_exists('categories', $validated)) {
            $event->categories()->sync($validated['categories']);
        } else {
            $event->categories()->sync([]);
        }

        return redirect()->route('events.show', $event->id)->with('success', 'Event updated successfully!');
    }

    /**
     * Remove the specified event from the db.
     * @param string $id: id of the event within the db
     * @return: a redirect back to the organisers dashboard
     */
    public function destroy(string $id)
    {
        $event = Event::where('id', $id)->firstOrFail();

        if (Auth::id() !== $event->user_id) {
            abort(403, 'UNAUTHORIZED ACTION');
        }

        // Check for existing bookings
        if ($event->bookings()->exists()) {
            return back()->with('error', 'Cannot delete an event that has active bookings.');
        }

        // remove categories from event, then delete
        $event->categories()->detach();
        $event->delete();

        return redirect()->route('events.dashboard')->with('success', 'Event deleted successfully.');
    }

    /**
     * @return: a view of the organisers dashboard
     */
    public function dashboard()
    {
        $organiserId = Auth::id();

        // Using raw sql to collect events for the organisers dashboard
        $sql = "
            SELECT
                events.id,
                events.title,
                events.date_time,
                events.capacity,
                COUNT(bookings.id) as amount_booked,
                (events.capacity - COUNT(bookings.id)) as available
            FROM
                events
            LEFT JOIN
                bookings ON events.id = bookings.event_id
            WHERE
                events.user_id = ?
            GROUP BY
                events.id, events.title, events.date_time, events.capacity
            ORDER BY
                events.date_time
        ";

        $eventsReport = DB::select($sql, array($organiserId));

        // turn the array into a collection of models (casts date_time into a DateTime object for formatting)
        $events = Event::hydrate($eventsReport);
        return view('events.dashboard')->with('events', $events);
    }


    /**
     * Filters the events based on the category id
     * @param int $category_id: the id of the category being filtered
     * @return string: the html of the updated event index
     */
    public function filter($category_id)
    {
        $query = Event::query()
            ->where('date_time', '>', now())
            ->orderBy('date_time', 'asc');

        if ($category_id !== 'all') {
            // access category table and filter by id
            $query->whereHas('categories', function ($q) use ($category_id) {
                $q->where('categories.id', $category_id);
            });
        }

        $events = $query->paginate(8);

        return view('events.partial_index')
            ->with('events', $events)
            ->render();
    }
}
