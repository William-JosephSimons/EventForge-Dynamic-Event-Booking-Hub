# EventForge-Dynamic-Event-Booking-Hub

Laravel event booking app with AJAX category filtering &amp; related events. Uni capstone project.

EventForge is a full-stack Laravel web app that was created as a capstone project for my uni. It allows organisers to craft and manage events while attendees discover, filter, and book spots. It also features AJAX-powered category tags and intelligent related event recommendations.

## Why EventForge?

Event platforms can trap users in endless, unfiltered lists. As a capstone project worth 90% of my grade in advanced web development, EventForge mitigates the issue with seamless role-based flows (organisers CRUD events with validation; attendees book with capacity checks) and discovery tools: multi-category assignments via many-to-many pivots, instant AJAX filtering on the home page, and "Related Events" surfacing up to three similar upcoming gatherings right on the details view.

## Quick Start

Bootstrap in under 5 minutes (PHP 8.1+, Composer, Node.js for assets; SQLite for zero-config DB):

1. Clone the repo:

    ```bash
    git clone https://github.com/yourusername/eventforge.git
    cd eventforge
    ```

2. Install PHP/JS dependencies:

    ```bash
    composer install
    npm install
    ```

3. Set up environment and assets:

    ```bash
    cp .env.example .env
    php artisan key:generate
    npm run build
    ```

4. Migrate and seed demo data (2 organisers, 10+ events across categories like "Tech" and "Social" for pagination/filtering):

    ```bash
    php artisan migrate --seed
    ```

5. Fire it up:
    ```bash
    php artisan serve
    ```
    Head to [http://127.0.0.1:8000](http://127.0.0.1:8000) for the public event list. Register as an attendee (with privacy consent) or log in as a seeded organiser (`organiser1@test.com` / `password`).

## Usage

EventForge delivers a polished experience for two roles, with Breeze auth, Blade views, and custom middleware enforcing access. All queries lean on Eloquent except the organiser dashboard's raw SQL join for efficiency.

### Core Flows

-   **Public Home/Event Listing**: Paginated (8/page) upcoming events (future date/time) showing title, date, time, location. Seeders ensure >8 items for demo. Click titles for details.
-   **Authentication**: Email/password login required post-public view. Top nav displays name/role; logout anytime. Attendee registration form auto-sets type, with server-validated privacy consent checkbox.
-   **Attendee Journey**:
    -   Browse/book: On details page, "Book Now" (if spots available/upcoming; prevents duplicates via unique validation).
    -   Manual Capacity Check: Controller queries current bookings vs. capacity—redirects with error if full (no overbooking!).
    -   My Bookings: Dedicated page lists booked events (title, date, time, location).
-   **organiser Journey**:
    -   CRUD Events: Create form (title ≤100 chars, optional desc textarea, future date/time, location ≤255 chars, capacity 1-1000; full Laravel validation). Edit pre-fills data; delete only if no bookings (error redirect).
    -   Dashboard Report: Raw SQL (`DB::select()`) joins events/bookings for per-event stats: title, date, capacity, booking count, remaining spots. Eloquent elsewhere.
-   **Multi-Category Support**:
    -   Organisers checkbox-select (e.g., "Tech", "Workshops") on create/edit—backed by `belongsToMany` in Event/Category models and `category_events` pivot. Updates use `sync()` to attach/detach without FK breaks.
-   **AJAX Filtering**:
    -   Home page tags filter events live (no reloads): Route returns Blade partial (`events/partials/listing`), injected via JS fetch. Pagination links inherit filter path (fixes initial load sans selection—simulates "All" category).
-   **Related Events**:
    -   Details page queries up to 3 upcoming events sharing ≥1 category.
