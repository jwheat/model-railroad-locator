<?php

namespace App\Livewire;

use App\Models\Club;
use App\Models\Layout;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;
use Livewire\Component;

class FollowButton extends Component
{
    #[Locked]
    public string $followableType;

    #[Locked]
    public int $followableId;

    public function mount(string $followableType, int $followableId): void
    {
        $this->followableType = $this->resolveType($followableType);
        $this->followableId = $followableId;
    }

    private function resolveType(string $type): string
    {
        return match ($type) {
            'layout' => Layout::class,
            'club' => Club::class,
            'member' => User::class,
            default => abort(404),
        };
    }

    private function followable(): Model
    {
        return $this->followableType::query()->findOrFail($this->followableId);
    }

    public function toggle(): void
    {
        if (! auth()->check()) {
            $this->redirectRoute('login');

            return;
        }

        $followable = $this->followable();

        if ($followable instanceof User && $followable->id === auth()->id()) {
            return;
        }

        $existing = $followable->followers()
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            $existing->delete();

            return;
        }

        $followable->followers()->create(['user_id' => auth()->id()]);
    }

    public function render()
    {
        $followable = $this->followable();
        $isSelf = $followable instanceof User && auth()->id() === $followable->id;

        return view('livewire.follow-button', [
            'following' => auth()->check() && $followable->isFollowedBy(auth()->user()),
            'count' => $followable->followerCount(),
            'isSelf' => $isSelf,
            'label' => match (true) {
                $followable instanceof Layout => 'this layout',
                $followable instanceof Club => 'this club',
                default => $followable->displayName(),
            },
        ]);
    }
}
