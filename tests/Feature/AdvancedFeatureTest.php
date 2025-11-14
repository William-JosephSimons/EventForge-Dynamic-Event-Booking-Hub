<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Category;


class AdvancedFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_organiser_can_assign_categories_when_creating_event(): void
    {
        // seed the db and create the organiser
        $this->seed();
        $organiser = User::factory()->organiser()->create();

        // get the first two category ids
        $expectedCategoryIds = Category::pluck('id')->take(2)->toArray();

        // add categories to the creation data for the event
        $eventData = Event::factory()->make(['user_id' => $organiser->id])->toArray();
        $eventData['categories'] = $expectedCategoryIds;

        // post the data to create the event
        $this->actingAs($organiser)->post(route('events.store'), $eventData);

        // collect the stored event
        $event = Event::latest('id')->first();

        // assert that the correct number of categories were attached.
        $this->assertCount(2, $event->categories);

        // get the ids of the categories attached to the stored event.
        $actualCategoryIds = $event->categories->pluck('id')->toArray();

        // sort both arrays to make the order consistent for comparison.
        sort($expectedCategoryIds);
        sort($actualCategoryIds);

        // assert that the attached categories are the ones specified at creation
        $this->assertEquals($expectedCategoryIds, $actualCategoryIds);
    }

    public function test_category_is_visible_on_event_details_page(): void
    {
        // seed db, collect an event and one of its categories
        $this->seed();
        $event = Event::first();
        $category = $event->categories->first();

        // check that it can be seen on its details page
        $response = $this->get(route('events.show', $event));
        $response->assertStatus(200);
        $response->assertSee($category->name);
    }

    public function test_public_can_filter_events_by_category_via_ajax(): void
    {
        // seed the db, and get the technology category
        $this->seed();
        $categoryToFilter = Category::where('name', 'Technology')->first();

        // create a specific event in this category
        $techEvent = Event::factory()->create();
        $techEvent->categories()->attach($categoryToFilter->id);

        // ensure other events exist
        Event::factory(5)->create();

        // run the filter and check only the tech event is present
        $response = $this->getJson(route('events.filter', $categoryToFilter->id));
        $response->assertStatus(200);
        $response->assertSee($techEvent->title);
        $response->assertDontSee(Event::latest('id')->first()->title);
    }

    public function test_related_events_shown_in_events_details(): void
    {
        // create a category that will be shared by two events
        $commonCategory = Category::create(['name' => 'Test Category']);

        // The event being viewed
        $mainEvent = Event::factory()->create();
        $mainEvent->categories()->attach($commonCategory->id);

        // An event that should appear in the related events section
        $relatedEvent = Event::factory()->create();
        $relatedEvent->categories()->attach($commonCategory->id);

        // A distractor event that should not appear
        $unrelatedEvent = Event::factory()->create();
        $unrelatedEvent->categories()->attach(Category::create(['name' => 'False Category'])->id);

        // visit the main event's detail page and check that the correct events are visible/invisible
        $response = $this->get(route('events.show', $mainEvent));
        $response->assertStatus(200);
        $response->assertSee('Related Events');
        $response->assertSee($relatedEvent->title);
        $response->assertDontSee($unrelatedEvent->title);
    }
}

