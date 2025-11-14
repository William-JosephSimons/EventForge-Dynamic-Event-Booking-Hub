<?php

namespace Database\Seeders;

use App\Models\CategoryEvent;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Category;
use App\Models\Booking;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the entire database in one file due to ease of access between all models
     */
    public function run(): void
    {
        // Create 6 different categories
        $categories = ['Technology', 'Business', 'Arts & Culture', 'Health & Wellness', 'Networking', 'Social Gathering'];
        foreach ($categories as $name) {
            Category::create(['name' => $name]);
        }

        // Create 3 Organisers
        $organiser1 = User::factory()->organiser()->create([
            'name' => 'Organiser 1',
            'email' => 'organiser1@example.com',
            'password' => Hash::make('password'),
        ]);

        $organiser2 = User::factory()->organiser()->create([
            'name' => 'Organiser 2',
            'email' => 'organiser2@example.com',
            'password' => Hash::make('password'),
        ]);

        // create an organiser with no events
        $organiser3 = User::factory()->organiser()->create([
            'name' => 'Organiser 3',
            'email' => 'organiser3@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create 10 Attendees
        $attendees = User::factory(10)->create();
        // Create one specific attendee for easy login
        $testAttendee = User::factory()->create([
            'name' => 'Test Attendee',
            'email' => 'attendee@example.com',
            'password' => Hash::make('password'),
        ]);
        $attendees->push($testAttendee);

        // Put 2 organisers into one collection for easy use of the random helper function
        $organisers = collect([$organiser1, $organiser2]);

        // Make 15 Events and assign each event to a random organiser before adding to the db
        $events = Event::factory(15)->make()->each(function ($event) use ($organisers) {
            $event->user_id = $organisers->random()->id;
            $event->save();
        });

        // Assign 1 to 3 categories to each event 
        $all_categories = Category::all();
        foreach ($events as $event) {
            $randomCategories = $all_categories->random(rand(1, 3));
            foreach ($randomCategories as $category) {
                CategoryEvent::create([
                    'category_id' => $category->id,
                    'event_id' => $event->id,
                ]);
            }
        }

        // Manually make some bookings 
        $bookableEvents = $events->take(10); // Use the first 10 events
        $bookingAttendees = $attendees->take(5); // Use the first 5 attendees


        // The attendees randomly book 1 to 3 events each
        foreach ($bookingAttendees as $attendee) {
            $eventsToBook = $bookableEvents->random(rand(1, 3));
            foreach ($eventsToBook as $event) {
                Booking::create([
                    'user_id' => $attendee->id,
                    'event_id' => $event->id,
                ]);
            }
        }
    }
}