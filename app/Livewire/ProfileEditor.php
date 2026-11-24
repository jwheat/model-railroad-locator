<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout as LayoutAttribute;
use Livewire\Component;
use Livewire\WithFileUploads;

#[LayoutAttribute('components.layouts.app')]
class ProfileEditor extends Component
{
    use WithFileUploads;

    public string $displayName = '';

    public string $bio = '';

    public string $city = '';

    public string $region = '';

    public string $country = 'US';

    public $avatar = null;

    public function mount(): void
    {
        $user = auth()->user();

        $this->displayName = (string) ($user->display_name ?: $user->name);
        $this->bio = (string) ($user->bio ?? '');
        $this->city = (string) ($user->city ?? '');
        $this->region = (string) ($user->region ?? '');
        $this->country = (string) ($user->country ?? 'US');
    }

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'displayName' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'city' => ['nullable', 'string', 'max:120'],
            'region' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'size:2'],
            'avatar' => ['nullable', 'image', 'max:5120'],
        ];
    }

    public function save(): void
    {
        $user = auth()->user();

        $this->validate();

        $oldAvatarPath = $user->avatar_path;

        $attributes = [
            'display_name' => filled($this->displayName) ? $this->displayName : null,
            'bio' => filled($this->bio) ? $this->bio : null,
            'city' => filled($this->city) ? $this->city : null,
            'region' => filled($this->region) ? $this->region : null,
            'country' => filled($this->country) ? $this->country : null,
        ];

        if ($this->avatar) {
            $user->forceFill(['avatar_path' => $this->avatar->store('avatars', 'public')])->save();

            if ($oldAvatarPath) {
                Storage::disk('public')->delete($oldAvatarPath);
            }
        }

        $user->forceFill($attributes)->save();

        session()->flash('status', 'Profile saved.');
        $this->redirectRoute('account.profile');
    }

    public function render()
    {
        return view('livewire.profile-editor', [
            'user' => auth()->user(),
        ]);
    }
}
