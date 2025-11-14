<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;

class GuestAccessTest extends TestCase
{

    use RefreshDatabase;

    public function test_a_guest_can_view_the_paginated_list_of_upcoming_events(): void
    {
        // Create 10 events
        Event::factory(10)->create();

        // Access the home page
        $response = $this->get(route('events.index'));

        $response->assertStatus(200);
        $response->assertViewIs('events.index');
        $response->assertSee('Upcoming Events');
        // Check that only 8 events are shown due to pagination
        $this->assertCount(8, $response->viewData('events'));
    }

    public function test_a_guest_can_view_a_specific_event_details_page(): void
    {
        // Create event
        $event = Event::factory()->create();

        // access details page of event
        $response = $this->get(route('events.show', $event));

        $response->assertStatus(200);
        $response->assertSee($event->title);
        $response->assertSee($event->description);
    }

    public function test_a_guest_is_redirected_when_accessing_protected_routes(): void
    {
        $this->get(route('bookings.index'))->assertRedirect(route('login'));
        $this->get(route('events.dashboard'))->assertRedirect(route('login'));
        $this->get(route('events.create'))->assertRedirect(route('login'));
    }

    public function test_a_guest_cannot_see_action_buttons_on_event_details_page(): void
    {
        // create event
        $event = Event::factory()->create();

        // access details page of event
        $response = $this->get(route('events.show', $event));

        $response->assertStatus(200);
        $response->assertDontSee('Book Now');
        $response->assertDontSee('Edit');
        $response->assertDontSee('Delete');
    }
}
