<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;

class AttendeeActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_successfully_register_as_an_attendee(): void
    {
        // register with valid data
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => 'on',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'attendee',
        ]);
    }

    public function test_a_registered_attendee_can_log_in_and_log_out(): void
    {
        // create user and login
        $user = User::factory()->create();

        $loginResponse = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $loginResponse->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        // logout and become guest
        $logoutResponse = $this->actingAs($user)->post(route('logout'));
        $logoutResponse->assertRedirect(route('events.index'));
        $this->assertGuest();
    }

    public function test_a_logged_in_attendee_can_book_an_available_upcoming_event(): void
    {
        // create a user and event
        $attendee = User::factory()->create();
        $event = Event::factory()->create(['capacity' => 10]);

        // book the event as the attendee
        $response = $this->actingAs($attendee)->post(route('bookings.store', $event));

        // available spots decreased by 1
        $this->assertEquals(9, $event->capacity - $attendee->bookedEvents()->count());
        $response->assertSessionHas('success', 'Event booked successfully!');
        $this->assertDatabaseHas('bookings', [
            'user_id' => $attendee->id,
            'event_id' => $event->id,
        ]);
    }

    public function test_after_booking_an_attendee_can_see_the_event_on_their_bookings_page(): void
    {
        // create attendee an and an event
        $attendee = User::factory()->create();
        $event = Event::factory()->create();

        // attendee books the event
        $attendee->bookedEvents()->attach($event->id);

        // attendee sees event on bookings page
        $response = $this->actingAs($attendee)->get(route('bookings.index'));
        $response->assertStatus(200);
        $response->assertSee($event->title);
    }

    public function test_an_attendee_cannot_book_the_same_event_more_than_once(): void
    {
        // create attendee and event
        $attendee = User::factory()->create();
        $event = Event::factory()->create();

        // attendee books the event
        $attendee->bookedEvents()->attach($event->id);

        // cannot book again
        $response = $this->actingAs($attendee)->post(route('bookings.store', $event));
        $response->assertSessionHas('error', 'You have already booked this event.');
        $this->assertEquals(1, $attendee->bookedEvents()->count());
    }

    public function test_an_attendee_cannot_book_a_full_event(): void
    {
        // create an event with 1 spot and have attendee book
        $event = Event::factory()->create(['capacity' => 1]);
        $attendee1 = User::factory()->create();
        $event->attendees()->attach($attendee1->id);

        // have another attendee book
        $attendee2 = User::factory()->create();
        $response = $this->actingAs($attendee2)->post(route('bookings.store', $event));

        // cannot book
        $response->assertSessionHas('error', 'This event is already full.');
        $this->assertDatabaseMissing('bookings', [
            'user_id' => $attendee2->id,
            'event_id' => $event->id,
        ]);
    }

    public function test_an_attendee_cannot_see_edit_or_delete_buttons_on_any_event_page(): void
    {
        // create an attendee and an event
        $attendee = User::factory()->create();
        $event = Event::factory()->create();

        // access event details page of event
        $response = $this->actingAs($attendee)->get(route('events.show', $event));
        $response->assertStatus(200);

        // check to see if the edit and delete routes are visible
        $editUrl = route('events.edit', $event);
        $deleteUrl = route('events.destroy', $event);
        $response->assertDontSee("<a href=$editUrl>");
        $response->assertDontSee("<form action=$deleteUrl>");
    }
}
