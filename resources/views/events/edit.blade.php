@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-2xl">
        <h1 class="text-center font-semibold p-6">Edit Event</h1>

        <form action="{{ route('events.update', $event->id) }}" method="POST">
            {{ csrf_field() }}
            {{ method_field('PUT') }}

            @if ($errors->any())
                <div class="mb-4" style="color: red;">
                    <strong class="font-semibold">Error!</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-4">
                <label class="font-bold mb-2">Title:</label>
                <input type="text" name="title" value="{{ old('title', $event->title) }}" required maxlength="100"
                    class="rounded w-full">
            </div>
            <div class="mb-4">
                <label class="font-bold mb-2">Description:</label>
                <textarea name="description" rows="5"
                    class="rounded w-full">{{ old('description', $event->description) }}</textarea>
            </div>
            <div class="mb-4">
                <label class="font-bold mb-2">Date and Time:</label>
                <!-- format time to mirror the selection -->
                <input type="datetime-local" name="date_time"
                    value="{{ old('date_time', ($event->date_time)->format('Y-m-d\TH:i')) }}" required
                    class="rounded w-full">
            </div>
            <div class="mb-4">
                <label class="font-bold mb-2">Location:</label>
                <input type="text" name="location" value="{{ old('location', $event->location) }}" required maxlength="255"
                    class="rounded w-full">
            </div>
            <div class="mb-4">
                <label class="font-bold mb-2">Capacity:</label>
                <input type="number" name="capacity" value="{{ old('capacity', $event->capacity) }}" required min="1"
                    max="1000" class="rounded w-full">
            </div>

            <div class="mb-4">
                <label class="font-bold mb-2">Categories:</label>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                    @foreach($categories as $category)
                        <label class="flex items-center">
                            <input class="rounded" type="checkbox" name="categories[]" value="{{ $category->id }}"
                                @if($event->categories->contains($category->id)) checked @endif>
                            {{ $category->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-between py-6">
                <button type="submit" class="text-indigo-600 font-semibold text-lg">
                    Update Event
                </button>
                <a href="{{ route('events.show', $event->id) }}" class="text-indigo-600 font-semibold text-lg">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection