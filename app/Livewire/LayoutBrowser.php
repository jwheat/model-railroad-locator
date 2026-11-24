<?php

namespace App\Livewire;

use App\LayoutScale;
use App\Models\Layout;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class LayoutBrowser extends Component
{
    use WithPagination;

    private const MILES_PER_KILOMETER = 1.609344;

    private const MAX_MAP_MARKERS = 500;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $scale = '';

    #[Url(except: 'newest')]
    public string $sort = 'newest';

    #[Url(as: 'view', except: 'list')]
    public string $viewMode = 'list';

    #[Url(as: 'unit', except: 'mi')]
    public string $distanceUnit = 'mi';

    #[Url(except: 100)]
    public int $radius = 100;

    public ?float $latitude = null;

    public ?float $longitude = null;

    public ?float $mapNorth = null;

    public ?float $mapSouth = null;

    public ?float $mapEast = null;

    public ?float $mapWest = null;

    public function updatedSearch(): void
    {
        $this->filtersChanged();
    }

    public function updatedScale(): void
    {
        $this->filtersChanged();
    }

    public function updatedSort(): void
    {
        $this->filtersChanged();
    }

    public function updatedRadius(): void
    {
        $this->filtersChanged();
    }

    public function setViewMode(string $mode): void
    {
        if (! in_array($mode, ['list', 'map'], true)) {
            return;
        }

        $this->viewMode = $mode;
        $this->resetMapBounds();
        $this->resetPage();

        $this->dispatchMapUpdate(fit: true);
    }

    public function setDistanceUnit(string $unit): void
    {
        if (! in_array($unit, ['mi', 'km'], true) || $unit === $this->distanceUnit) {
            return;
        }

        $currentKilometers = $this->radiusInKilometers();

        $this->distanceUnit = $unit;
        $this->radius = $this->nearestRadius(
            $unit === 'mi' ? $currentKilometers / self::MILES_PER_KILOMETER : $currentKilometers,
        );

        $this->filtersChanged();
    }

    public function setCoordinates(float $latitude, float $longitude): void
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->sort = 'distance';
        $this->resetMapBounds();
        $this->resetPage();

        $this->dispatchMapUpdate(fit: true);
    }

    public function clearLocation(): void
    {
        $this->latitude = null;
        $this->longitude = null;
        $this->sort = 'newest';
        $this->resetMapBounds();
        $this->resetPage();

        $this->dispatchMapUpdate(fit: true);
    }

    public function setMapBounds(float $north, float $south, float $east, float $west): void
    {
        $north = min(90, max(-90, $north));
        $south = min(90, max(-90, $south));
        $east = min(180, max(-180, $east));
        $west = min(180, max(-180, $west));

        if ($north <= $south) {
            return;
        }

        if (
            $this->mapNorth === $north
            && $this->mapSouth === $south
            && $this->mapEast === $east
            && $this->mapWest === $west
        ) {
            return;
        }

        $this->mapNorth = $north;
        $this->mapSouth = $south;
        $this->mapEast = $east;
        $this->mapWest = $west;

        $this->dispatchMapUpdate(fit: false);
    }

    /**
     * @return array<int, int>
     */
    public function radiusOptions(): array
    {
        return [10, 25, 50, 100, 150, 250, 500, 1000];
    }

    public function radiusInKilometers(): float
    {
        $radius = max(1, $this->radius);

        return $this->distanceUnit === 'mi'
            ? $radius * self::MILES_PER_KILOMETER
            : (float) $radius;
    }

    public function render()
    {
        $mapLayouts = $this->mapMarkers();

        return view('livewire.layout-browser', [
            'layouts' => $this->applySort($this->matchingQuery())->paginate(12),
            'mapLayouts' => $mapLayouts,
            'unmappedCount' => $this->unmappedCount(),
            'scales' => LayoutScale::options(),
            'radiusOptions' => $this->radiusOptions(),
        ]);
    }

    private function filtersChanged(): void
    {
        $this->resetPage();
        $this->resetMapBounds();
        $this->dispatchMapUpdate(fit: true);
    }

    private function dispatchMapUpdate(bool $fit): void
    {
        $this->dispatch(
            'layout-map-updated',
            layouts: $this->mapMarkers(),
            userLocation: $this->userLocation(),
            fit: $fit,
        );
    }

    private function matchingQuery(): Builder
    {
        $query = Layout::query()
            ->visible()
            ->with(['owner', 'club'])
            ->withCount('posts')
            ->search($this->search);

        if ($this->scale !== '' && LayoutScale::tryFrom($this->scale)) {
            $query->where('scale', $this->scale);
        }

        return $query;
    }

    private function applySort(Builder $query): Builder
    {
        if ($this->sort === 'distance' && $this->hasLocation()) {
            return $query->near($this->latitude, $this->longitude, $this->radiusInKilometers());
        }

        if ($this->sort === 'popular') {
            return $query->orderByDesc('posts_count');
        }

        return $query->latest();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function mapMarkers(): array
    {
        $query = Layout::query()
            ->visible()
            ->search($this->search)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($this->scale !== '' && LayoutScale::tryFrom($this->scale)) {
            $query->where('scale', $this->scale);
        }

        if ($this->sort === 'distance' && $this->hasLocation()) {
            $query->near($this->latitude, $this->longitude, $this->radiusInKilometers());
        }

        if ($this->hasMapBounds()) {
            $query->withinBounds(
                $this->mapNorth,
                $this->mapSouth,
                $this->mapEast,
                $this->mapWest,
            );
        }

        return $query
            ->limit(self::MAX_MAP_MARKERS)
            ->get()
            ->map(fn (Layout $layout) => [
                'id' => $layout->id,
                'name' => $layout->name,
                'url' => route('layouts.show', $layout),
                'latitude' => (float) $layout->latitude,
                'longitude' => (float) $layout->longitude,
                'scale' => $layout->scale->value,
                'scaleLabel' => $layout->scale->label(),
                'place' => collect([$layout->city, $layout->region, $layout->country])
                    ->filter()
                    ->implode(', '),
                'cover' => $layout->coverUrl(),
                'distance' => $layout->distanceLabel($this->distanceUnit),
            ])
            ->values()
            ->all();
    }

    private function unmappedCount(): int
    {
        return $this->matchingQuery()
            ->where(fn (Builder $query) => $query
                ->whereNull('latitude')
                ->orWhereNull('longitude'))
            ->count();
    }

    /**
     * @return array{latitude: float, longitude: float}|null
     */
    private function userLocation(): ?array
    {
        if (! $this->hasLocation()) {
            return null;
        }

        return [
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
        ];
    }

    private function nearestRadius(float $value): int
    {
        $options = $this->radiusOptions();

        usort($options, fn (int $a, int $b) => abs($a - $value) <=> abs($b - $value));

        return $options[0];
    }

    private function hasLocation(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    private function hasMapBounds(): bool
    {
        return $this->mapNorth !== null
            && $this->mapSouth !== null
            && $this->mapEast !== null
            && $this->mapWest !== null;
    }

    private function resetMapBounds(): void
    {
        $this->mapNorth = null;
        $this->mapSouth = null;
        $this->mapEast = null;
        $this->mapWest = null;
    }
}
