<?php

namespace App\Livewire;

use App\LayoutScale;
use App\Models\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class LayoutBrowser extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $scale = '';

    #[Url(except: 'newest')]
    public string $sort = 'newest';

    public ?float $latitude = null;

    public ?float $longitude = null;

    public int $radius = 100;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedScale(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function updatedRadius(): void
    {
        $this->resetPage();
    }

    public function setCoordinates(float $latitude, float $longitude): void
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->sort = 'distance';
        $this->resetPage();
    }

    public function clearLocation(): void
    {
        $this->latitude = null;
        $this->longitude = null;
        $this->sort = 'newest';
        $this->resetPage();
    }

    public function render()
    {
        $query = Layout::query()
            ->visible()
            ->with(['owner', 'club'])
            ->withCount('posts')
            ->search($this->search);

        if ($this->scale !== '' && LayoutScale::tryFrom($this->scale)) {
            $query->where('scale', $this->scale);
        }

        if ($this->sort === 'distance' && $this->latitude !== null && $this->longitude !== null) {
            $query->near($this->latitude, $this->longitude, $this->radius);
        } elseif ($this->sort === 'popular') {
            $query->orderByDesc('posts_count');
        } else {
            $query->latest();
        }

        return view('livewire.layout-browser', [
            'layouts' => $query->paginate(12),
            'scales' => LayoutScale::options(),
        ]);
    }
}
