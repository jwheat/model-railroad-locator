<?php

namespace App\Livewire;

use App\LayoutScale;
use App\LayoutVisibility;
use App\Models\Layout;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout as LayoutAttribute;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

#[LayoutAttribute('components.layouts.app')]
class LayoutEditor extends Component
{
    use AuthorizesRequests, WithFileUploads;

    #[Locked]
    public ?int $layoutId = null;

    public string $name = '';

    public string $description = '';

    public string $scale = 'HO';

    public string $era = '';

    public string $city = '';

    public string $region = '';

    public string $country = 'US';

    public ?string $latitude = null;

    public ?string $longitude = null;

    public string $visibility = 'public';

    public $cover = null;

    public function mount(?Layout $layout = null): void
    {
        if ($layout?->exists) {
            $this->authorize('update', $layout);

            $this->layoutId = $layout->id;
            $this->name = $layout->name;
            $this->description = (string) $layout->description;
            $this->scale = $layout->scale->value;
            $this->era = (string) $layout->era;
            $this->city = (string) $layout->city;
            $this->region = (string) $layout->region;
            $this->country = (string) ($layout->country ?: 'US');
            $this->latitude = $layout->latitude;
            $this->longitude = $layout->longitude;
            $this->visibility = $layout->visibility->value;
        } else {
            $this->authorize('create', Layout::class);
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'scale' => ['required', Rule::enum(LayoutScale::class)],
            'era' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'region' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'size:2'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'visibility' => ['required', Rule::enum(LayoutVisibility::class)],
            'cover' => ['nullable', 'image', 'max:5120'],
        ];
    }

    public function useMyLocation(float $latitude, float $longitude): void
    {
        $this->latitude = round($latitude, 7);
        $this->longitude = round($longitude, 7);
    }

    public function save()
    {
        $this->authorize($this->layoutId ? 'update' : 'create', $this->layoutId ? Layout::findOrFail($this->layoutId) : Layout::class);

        $this->validate();

        $attributes = [
            'name' => $this->name,
            'description' => $this->description ?: null,
            'scale' => $this->scale,
            'era' => $this->era ?: null,
            'city' => $this->city ?: null,
            'region' => $this->region ?: null,
            'country' => $this->country ?: null,
            'latitude' => $this->latitude !== null && $this->latitude !== '' ? (float) $this->latitude : null,
            'longitude' => $this->longitude !== null && $this->longitude !== '' ? (float) $this->longitude : null,
            'visibility' => $this->visibility,
        ];

        if ($this->cover) {
            $attributes['cover_image_path'] = $this->cover->store('layouts', 'public');
        }

        if ($this->layoutId) {
            $layout = Layout::findOrFail($this->layoutId);
            $layout->update($attributes);
        } else {
            $layout = auth()->user()->layouts()->create($attributes);
            $this->layoutId = $layout->id;
        }

        session()->flash('status', 'Layout saved.');

        $this->redirectRoute('layouts.show', $layout);
    }

    public function render()
    {
        return view('livewire.layout-editor', [
            'scales' => LayoutScale::options(),
            'visibilities' => LayoutVisibility::options(),
        ]);
    }
}
