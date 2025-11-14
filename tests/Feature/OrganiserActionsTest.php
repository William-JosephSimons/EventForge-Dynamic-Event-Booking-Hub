<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;

class OrganiserActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_organiser_can_log_in_and_view_their_specific_dashboard(): void
    {
        // create and organiser and an event they have created
        $organiser = User::factory()->organiser()->create();
        $myEvent = Event::factory()->create(['user_id' => $organiser->id]);

        // create an event that belongs to another organiser
        $otherEvent = Event::factory()->create();

        // access organisers dashboard and only see their event
        $response = $this->actingAs($organiser)->get(route('events.dashboard'));
        $response->assertStatus(200);
        $response->assertSee($myEvent->title);
        $response->assertDontSee($otherEvent->title);
    }

    public function test_an_organiser_can_successfully_create_an_event_with_valid_data(): void
    {
        // create the organiser and prepare the event data without saving it.
        $organiser = User::factory()->organiser()->create();
        $eventData = Event::factory()->make([
            'title' => 'New Tech Conference',
            'user_id' => null // make with no user_id as it will be stored when routed
        ])->toArray();

        // post the data to the store route to create the event.
        $response = $this->actingAs($organiser)->post(route('events.store'), $eventData);

        // find the event that was just created and check the redirect.
        $this->assertDatabaseHas('events', ['title' => 'New Tech Conference']);
        $newEvent = Event::latest('id')->first(); // get the most recently created event.

        $response->assertRedirect(route('events.show', $newEvent->id));

    }

    #[DataProvider('invalidEventDataProvider')]
    public function test_an_organiser_receives_validation_errors_for_invalid_event_data(array $invalidData, string $errorField): void
    {
        // create the organiser 
        $organiser = User::factory()->organiser()->create();

        // start with valid data and merge the specific invalid data for this test run
        $validData = [
            'title' => 'Valid Title',
            'description' => 'Valid description.',
            'date_time' => now()->addMonth()->toDateTimeString(),
            'location' => 'Valid Location',
            'capacity' => 100,
        ];
        $testData = array_merge($validData, $invalidData);

        // attempt to store
        $response = $this->actingAs($organiser)->post(route('events.store'), $testData);

        // assert error
        $response->assertSessionHasErrors($errorField);
    }

    // use this data for each call to validation errors test
    public static function invalidEventDataProvider(): array
    {
        return [
            // key              $invalidData    $errorField
            'Missing title' => [['title' => ''], 'title'],
            'Title too long' => [['title' => str_repeat('a', 101)], 'title'],
            'Missing date_time' => [['date_time' => ''], 'date_time'],
            'date_time in the past' => [['date_time' => now()->subDay()->toDateTimeString()], 'date_time'],
            'Missing location' => [['location' => ''], 'location'],
            'Location too long' => [['location' => str_repeat('a', 256)], 'location'],
            'Missing capacity' => [['capacity' => ''], 'capacity'],
            'Capacity less than 1' => [['capacity' => 0], 'capacity'],
            'Capacity greater than 1000' => [['capacity' => 1001], 'capacity'],
            'Capacity is not an integer' => [['capacity' => 'not-a-number'], 'capacity'],
        ];
    }

    public function test_an_organiser_can_successfully_update_an_event_they_own(): void
    {
        // create organiser and their event
        $organiser = User::factory()->organiser()->create();
        $event = Event::factory()->create(['user_id' => $organiser->id]);

        // update old event data with new data
        $updateData = ['title' => 'Updated Event Title'];
        $response = $this->actingAs($organiser)->put(route('events.update', $event), array_merge($event->toArray(), $updateData));

        // check the update
        $response->assertRedirect(route('events.show', $event));
        $this->assertDatabaseHas('events', ['id' => $event->id, 'title' => 'Updated Event Title']);
    }

    public function test_an_organiser_cannot_update_an_event_created_by_another_organiser(): void
    {
        // create 2 organisers 
        $organiser1 = User::factory()->organiser()->create();
        $organiser2 = User::factory()->organiser()->create();

        // create event made by organiser 2
        $event = Event::factory()->create(['user_id' => $organiser2->id]);

        // assert that organiser 1 can't update it
        $response = $this->actingAs($organiser1)->put(route('events.update', $event), ['title' => 'Malicious Update']);
        $response->assertStatus(403);
    }

    public function test_an_organiser_can_delete_an_event_they_own_that_has_no_bookings(): void
    {
        // create an organiser and their event
        $organiser = User::factory()->organiser()->create();
        $event = Event::factory()->create(['user_id' => $organiser->id]);

        // assert the event is deleted
        $response = $this->actingAs($organiser)->delete(route('events.destroy', $event));
        $response->assertRedirect(route('events.dashboard'));
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_an_organiser_cannot_delete_an_event_that_has_active_bookings(): void
    {
        // create an organiser and their event
        $organiser = User::factory()->organiser()->create();
        $event = Event::factory()->create(['user_id' => $organiser->id]);

        // create an attendee that has booked the event
        $attendee = User::factory()->create();
        $event->attendees()->attach($attendee->id);

        // cannot delete the event
        $response = $this->actingAs($organiser)->delete(route('events.destroy', $event));
        $response->assertSessionHas('error', 'Cannot delete an event that has active bookings.');
        $this->assertDatabaseHas('events', ['id' => $event->id]);
    }
}

