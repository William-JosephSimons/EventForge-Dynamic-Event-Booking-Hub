@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-center font-semibold p-4" style='font-size: 40px;'>Upcoming Events</h1>
        <div class="flex justify-center gap-2 py-2 text-indigo-600 font-semibold" id="category-filters">
            <a href="_" class="category-filter" data-id="all">All Events</a>
            @foreach($categories as $category)
                <a href="_" class="category-filter px-3" data-id="{{ $category->id }}">{{ $category->name }}</a>
            @endforeach
        </div>

        <div id="event-list-container">
            @include('events.partial_index', ['events' => $events])
        </div>
    </div>

    <script>
        // category nav bar
        const filterContainer = document.getElementById('category-filters');

        // events list
        const eventListContainer = document.getElementById('event-list-container');

        // Listen for clicks on the entire document,
        // specifically checking categories and paginator
        document.addEventListener('click', function (e) {

            // Check if a category filter was clicked
            const categoryFilter = e.target.closest('.category-filter');
            if (categoryFilter) {
                e.preventDefault();

                // category href now fetches events using filter and displays the partial_index
                const categoryId = e.target.dataset.id;
                const url = "{{ route('events.filter', 'all') }}".replace('all', categoryId);

                eventListContainer.innerHTML = '<p class="text-center">Loading...</p>';

                fetchAndUpdate(url);
            }

            // Check if a pagination link was clicked 
            // (go into partial event list container -> go into pagination class div -> find the anchor)
            const paginationLink = e.target.closest('#event-list-container .pagination a');
            if (paginationLink) {
                e.preventDefault();

                eventListContainer.innerHTML = '<p class="text-center">Loading...</p>';

                // uses filter base path to ensure correct fetching, paginationLink appends page=...
                fetchAndUpdate(paginationLink.href);
            }
        });

        function fetchAndUpdate(url) {
            fetch(url)
                .then(function (response) {
                    return response.text();
                })
                .then(function (html) {
                    eventListContainer.innerHTML = html;
                })
                .catch(function (error) {
                    console.error('Error fetching events:', error);
                    eventListContainer.innerHTML = '<p class="text-center">Failed to load events.</p>';
                });
        }
    </script>
@endsection