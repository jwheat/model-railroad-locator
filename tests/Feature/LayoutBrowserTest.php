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
            ->assertSeeInOrder(['Near Line', 'Far Line']);
    }
}
