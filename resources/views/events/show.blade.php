@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Status message for working with event -->
        @if(session('success'))
            <div class="mb-4" style="color: greenyellow;">
                <strong class="font-semibold">{{ session('success') }}</strong>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4" style="color: red;">
                <strong class="font-semibold">{{ session('error') }}</strong>
            </div>
        @endif

        <h1 class="text-center font-semibold p-6" style="font-size: 40px;">{{ $event->title }}</h1>
        <div class="bg-white rounded-lg">
            <div class="p-6">
                <p class="mb-6"><strong>Organiser:</strong> {{ $event->organiser->name }}</p>
                <p class="mb-6"><strong>Location:</strong> {{ $event->location }}</p>
                <p class="mb-6"><strong>Date:</strong> {{ ($event->date_time)->format('M j, Y') }}</p>
                <p><strong>Time:</strong> {{ ($event->date_time)->format('g:i A') }}</p>
                @if($event->description)
                    <p class="mt-6"><strong>About the Event</strong></p>
                    <p> {{ $event->description }}</p>
                @endif
            </div>
        </div>


        <div class="flex bg-gray-100 p-6 justify-between">
            <div>
                <p class="text-lg">
                    <strong>Capacity:</strong> {{ $event->capacity }}
                </p>
                <p class="text-lg font-semibold" style="color: greenyellow;">
                    <strong>Available Spots:</strong> {{ $event->capacity - $event->bookings->count() }}
                </p>
            </div>

            <div class="py-2">
                @auth
                    <!-- Booking button is only viewable by attendees -->
                    @if(Auth::user()->isAttendee())
                        @if($hasBooked)
                            <p class="font-semibold text-lg" style="color: black;">Already Booked</p>
                        @elseif($event->date_time < now())
                            <p class="font-semibold text-lg" style="color: black;">Event has passed</p>
                        @elseif($event->bookings->count() >= $event->capacity)
                            <p class="font-semibold text-lg" style="color: black;">Event Full</p>
                        @else
                            <form action="{{ route('bookings.store', $event) }}" method="POST">
                                {{ csrf_field() }}
                                <button type="submit" class="text-indigo-600 font-semibold text-lg">
                                    Book Now
                                </button>
                            </form>
                        @endif
                        <!-- Edit and Delete buttons only viewable by organisers -->
                    @elseif(Auth::user()->isOrganiser())
                        @if(Auth::id() === $event->user_id)
                            <div class="flex gap-6">
                                <a href="{{ route('events.edit', $event->id) }}" class="text-indigo-600 font-semibold text-lg">Edit</a>
                                <form action="{{ route('events.destroy', $event) }}" method="POST">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <button type="submit" class="text-indigo-600 font-semibold text-lg">Delete</button>
                                </form>
                            </div>
                        @endif
                    @endif
                @endauth
            </div>
        </div>

        <div>
            <!-- if the event has categories, display them -->
            @if($event->categories->isNotEmpty())
                <div class="flex justify-center font-semibold">
                    @foreach($event->categories as $category)
                        <div class="px-6">
                            {{ $category->name }}
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- if the event has related events, display them using the partial -->
        @if($relatedEvents->isNotEmpty())
            <div class="container mx-auto px-4 py-8 mt-6">
                <h2 class="text-center font-semibold p-6" style="font-size: 30px;">Related Events</h2>
                @include('events.partial_index', ['events' => $relatedEvents])
            </div>
        @endif
    </div>
    </div>
@endsection