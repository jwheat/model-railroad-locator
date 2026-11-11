<?php

namespace App\Livewire;

use App\Models\Layout;
use App\Models\Post;
use App\ReactionType;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ReactionBar extends Component
{
    #[Locked]
    public string $reactionableType;

    #[Locked]
    public int $reactionableId;

    public function mount(string $reactionableType, int $reactionableId): void
    {
        $this->reactionableType = $this->resolveType($reactionableType);
        $this->reactionableId = $reactionableId;
    }

    private function resolveType(string $type): string
    {
        return match ($type) {
            'post' => Post::class,
            'layout' => Layout::class,
            default => abort(404),
        };
    }

    private function reactionable(): Model
    {
        return $this->reactionableType::query()->findOrFail($this->reactionableId);
    }

    public function toggle(string $type): void
    {
        if (! auth()->check()) {
            $this->redirectRoute('login');

            return;
        }

        $reactionType = ReactionType::tryFrom($type);

        if (! $reactionType) {
            return;
        }

        $reactionable = $this->reactionable();
        $existing = $reactionable->reactions()
            ->where('user_id', auth()->id())
            ->first();

        if ($existing?->type === $reactionType) {
            $existing->delete();

            return;
        }

        if ($existing) {
            $existing->update(['type' => $reactionType]);

            return;
        }

        $reactionable->reactions()->create([
            'user_id' => auth()->id(),
            'type' => $reactionType,
        ]);
    }

    public function render()
    {
        $reactionable = $this->reactionable();

        $counts = $reactionable->reactions()
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $mine = auth()->check()
            ? $reactionable->reactions()->where('user_id', auth()->id())->value('type')
            : null;

        $mine = $mine instanceof ReactionType ? $mine : ReactionType::tryFrom((string) $mine);

        return view('livewire.reaction-bar', [
            'counts' => $counts,
            'mine' => $mine,
            'types' => ReactionType::cases(),
        ]);
    }
}
