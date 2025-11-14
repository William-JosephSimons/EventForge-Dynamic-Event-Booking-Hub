@if($events->isEmpty())
    <p class="text-center">No upcoming events match the selected category.</p>
@else
    <div class="grid gap-6">
        @foreach($events as $event)
            <div class="bg-white rounded-lg">
                <div class="p-6">
                    <h2 class="text-xl font-semibold">
                        <a href="{{ route('events.show', $event->id) }}" class="text-indigo-600">{{ $event->title }}</a>
                    </h2>
                    <div class="text-gray-600">
                        <p class="mb-1"><strong>Date:</strong>
                            {{ ($event->date_time)->format('M j, Y') }}</p>
                        <p class="mb-1"><strong>Time:</strong> {{ ($event->date_time)->format('g:i A') }}
                        </p>
                        <p><strong>Location:</strong> {{ $event->location }}</p>
                        <p><strong>Categories:</strong>
                            @foreach ($event->categories as $category)
                                {{ $category->name}}@if (!$loop->last),@endif
                            @endforeach
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
        @if ($events instanceof Illuminate\Pagination\LengthAwarePaginator)
            <div class="p-6 pagination">
                {{ $events->links() }}
            </div>
        @endif
    </div>
@endif