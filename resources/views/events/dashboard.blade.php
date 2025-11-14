@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-center font-semibold p-6" style='font-size: 40px;'>Organiser Dashboard</h1>
        @if(session('success'))
            <div class="mb-4" style="color: greenyellow;">
                <strong class="font-semibold">{{ session('success') }}</strong>
            </div>
        @endif

        @if ($events->isEmpty())
            <p class="text-center">You have not created any events yet</p>
        @else
            <div class="bg-white rounded-lg">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="bg-gray-100 uppercase">
                                Event
                            </th>
                            <th class="bg-gray-100 uppercase">
                                Date & Time
                            </th>
                            <th class="bg-gray-100 uppercase">
                                Capacity
                            </th>
                            <th class="bg-gray-100 uppercase">
                                Amount Currently Booked
                            </th>
                            <th class="bg-gray-100 uppercase">
                                Available
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $event)
                            <tr style="text-align:center">
                                <td>
                                    <a href="{{ route('events.show', $event->id) }}"
                                        class="text-indigo-600 font-semibold">{{ $event->title }}</a>
                                </td>
                                <td>
                                    {{ ($event->date_time)->format('M j, Y - g:i A') }}
                                </td>
                                <td>{{ $event->capacity }}</td>
                                <td>{{ $event->amount_booked }}</td>
                                <td>{{ $event->available }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection