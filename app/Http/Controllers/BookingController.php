<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * @return: a view of the attendees bookings
     */
    public function index()
    {
        $bookings = Auth::user()
            ->bookedEvents()
            ->orderBy('date_time', 'asc')
            ->get();

        return view('bookings.index')->with('bookings', $bookings);
    }

    /**
     * Stores an attendees booking
     * @param Request: the request to make a booking
     * @param Event: the event that the attendee wants to book
     * @return: A redirect back to the bookings.index page with success or error
     */
    public function store(Request $request, Event $event)
    {
        // using the event object rather than using request validation
        // therefore, manually storing validation errors with 'back->with()'
        $user = Auth::user();

        // Prevent organisers from booking
        if ($user->isOrganiser()) {
            return back()->with('error', 'Organisers cannot book events.');
        }

        // Check if the event is in the past
        if ($event->date_time < now()) {
            return back()->with('error', 'This event has already passed.');
        }

        // Check if the event is full
        $currentBookings = $event->bookings()->count();
        if ($currentBookings >= $event->capacity) {
            return back()->with('error', 'This event is already full.');
        }

        // Check if the user has already booked this event
        if ($user->bookedEvents()->where('event_id', $event->id)->exists()) {
            return back()->with('error', 'You have already booked this event.');
        }

        // Create the booking
        Booking::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        return redirect()->back()->with('success', 'Event booked successfully!');
    }
}
