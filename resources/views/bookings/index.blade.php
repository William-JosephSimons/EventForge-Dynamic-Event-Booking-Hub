@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-center font-semibold p-6" style='font-size: 40px;'>My Bookings</h1>

        @if($bookings->isEmpty())
            <p class="text-center">You have not booked any events yet.</p>
        @else
            <div class="bg-white rounded-lg">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="bg-gray-100 uppercase">
                                Event Title
                            </th>
                            <th class="bg-gray-100 uppercase">
                                Date & Time
                            </th>
                            <th class="bg-gray-100 uppercase">
                                Location
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $event)
                            <tr style="text-align: center;">
                                <td>
                                    <a href="{{ route('events.show', $event->id) }}" class="text-indigo-600 font-semibold">
                                        {{ $event->title }}
                                    </a>
                                </td>
                                <td>{{ ($event->date_time)->format('M j, Y - g:i A') }}</td>
                                <td>{{ $event->location }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection