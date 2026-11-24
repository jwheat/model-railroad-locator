<?php

namespace Tests\Feature;

use App\LayoutScale;
use App\Livewire\LayoutBrowser;
use App\Models\Layout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LayoutBrowserTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_public_layouts_are_listed(): void
    {
        Layout::factory()->create(['name' => 'Public Line']);
        Layout::factory()->private()->create(['name' => 'Secret Line']);

        Livewire::test(LayoutBrowser::class)
            ->assertSee('Public Line')
            ->assertDontSee('Secret Line');
    }

    public function test_search_filters_by_name_and_city(): void
    {
        Layout::factory()->create([
            'name' => 'Cascade Valley & Western',
            'city' => 'Portland',
        ]);
        Layout::factory()->create([
            'name' => 'Tidewater Belt Line',
            'city' => 'Norfolk',
        ]);

        Livewire::test(LayoutBrowser::class)
            ->set('search', 'Portland')
            ->assertSee('Cascade Valley & Western')
            ->assertDontSee('Tidewater Belt Line');
    }

    public function test_scale_filter_narrows_results(): void
    {
        Layout::factory()->create(['name' => 'HO Line', 'scale' => LayoutScale::HO]);
        Layout::factory()->create(['name' => 'N Line', 'scale' => LayoutScale::N]);

        Livewire::test(LayoutBrowser::class)
            ->set('scale', 'N')
            ->assertSee('N Line')
            ->assertDontSee('HO Line');
    }

    public function test_distance_sort_orders_nearest_first(): void
    {
        Layout::factory()->create([
            'name' => 'Near Line',
            'latitude' => 45.52,
            'longitude' => -122.68,
        ]);
        Layout::factory()->create([
            'name' => 'Far Line',
            'latitude' => 40.71,
            'longitude' => -74.01,
        ]);

        Livewire::test(LayoutBrowser::class)
            ->call('setCoordinates', 45.5152, -122.6784)
            ->set('radius', 5000)
            ->assertSeeInOrder(['Near Line', 'Far Line']);
    }

    public function test_map_defaults_to_miles_and_only_includes_layouts_with_coordinates(): void
    {
        Layout::factory()->create([
            'name' => 'Mapped Line',
            'latitude' => 45.52,
            'longitude' => -122.68,
        ]);
        Layout::factory()->create([
            'name' => 'Unmapped Line',
            'latitude' => null,
            'longitude' => null,
        ]);

        Livewire::test(LayoutBrowser::class)
            ->call('setViewMode', 'map')
            ->assertSet('viewMode', 'map')
            ->assertSet('distanceUnit', 'mi')
            ->call('setCoordinates', 45.5152, -122.6784)
            ->assertViewHas('mapLayouts', function (array $layouts) {
                return count($layouts) === 1
                    && $layouts[0]['name'] === 'Mapped Line'
                    && str_ends_with($layouts[0]['distance'], ' mi');
            })
            ->assertViewHas('unmappedCount', 1);
    }

    public function test_distance_unit_can_be_switched_to_kilometres(): void
    {
        Layout::factory()->create([
            'name' => 'Mapped Line',
            'latitude' => 45.52,
            'longitude' => -122.68,
        ]);

        Livewire::test(LayoutBrowser::class)
            ->call('setCoordinates', 45.5152, -122.6784)
            ->call('setDistanceUnit', 'km')
            ->assertSet('distanceUnit', 'km')
            ->assertViewHas('mapLayouts', fn (array $layouts) => str_ends_with($layouts[0]['distance'], ' km'));
    }

    public function test_map_bounds_filter_markers_without_filtering_the_list(): void
    {
        Layout::factory()->create([
            'name' => 'Portland Line',
            'latitude' => 45.52,
            'longitude' => -122.68,
        ]);
        Layout::factory()->create([
            'name' => 'New York Line',
            'latitude' => 40.71,
            'longitude' => -74.01,
        ]);

        Livewire::test(LayoutBrowser::class)
            ->call('setMapBounds', 49.0, 44.0, -120.0, -125.0)
            ->assertViewHas('mapLayouts', fn (array $layouts) => collect($layouts)->pluck('name')->all() === ['Portland Line'])
            ->assertSee('Portland Line')
            ->assertSee('New York Line');
    }

    public function test_radius_is_converted_from_miles_to_kilometres(): void
    {
        $component = Livewire::test(LayoutBrowser::class);

        $this->assertEqualsWithDelta(160.9344, $component->instance()->radiusInKilometers(), 0.0001);

        $component->call('setDistanceUnit', 'km')
            ->assertSet('radius', 150);

        $this->assertEqualsWithDelta(150.0, $component->instance()->radiusInKilometers(), 0.0001);
    }

    public function test_view_can_be_switched_to_map_and_back_to_list(): void
    {
        Livewire::test(LayoutBrowser::class)
            ->call('setViewMode', 'map')
            ->assertSet('viewMode', 'map')
            ->call('setViewMode', 'list')
            ->assertSet('viewMode', 'list');
    }
}
