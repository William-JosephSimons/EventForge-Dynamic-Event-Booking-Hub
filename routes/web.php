<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BookingController;


// events.index is the home page for all users
// manually implement show and index routes to exclude them from auth middelware group
Route::get('/', [EventController::class, 'index'])->name('events.index');
Route::get('/dashboard', function () {
    return redirect()->route('events.index');
})->name('dashboard');

// filtered routing for events based on category
Route::get('/events/filter/{category_id}', [EventController::class, 'filter'])->name('events.filter');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:attendee')->group(function () {
        // Store route contains event being booked. Better than passing even attributes through hidden fields
        Route::post('/bookings/{event}', [BookingController::class, 'store'])->name('bookings.store');
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    });

    Route::middleware('role:organiser')->group(function () {

        // dashboard route for organisers
        Route::get('/org_dashboard', [EventController::class, 'dashboard'])->name('events.dashboard');

        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::resource('events', EventController::class)->except(['index', 'show']);
    });
});


Route::get('/events/{event_id}', [EventController::class, 'show'])->name('events.show');

Route::get('/privacy-policy', function () {
    return view('pages.privacy');
})->name('privacy.policy');

require __DIR__ . '/auth.php';
