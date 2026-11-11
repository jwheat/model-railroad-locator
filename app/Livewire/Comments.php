<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\Layout;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Comments extends Component
{
    use AuthorizesRequests;

    #[Locked]
    public string $commentableType;

    #[Locked]
    public int $commentableId;

    public string $body = '';

    public ?int $replyTo = null;

    public function mount(string $commentableType, int $commentableId): void
    {
        $this->commentableType = $this->resolveType($commentableType);
        $this->commentableId = $commentableId;
    }

    private function resolveType(string $type): string
    {
        return match ($type) {
            'post' => Post::class,
            'layout' => Layout::class,
            default => abort(404),
        };
    }

    private function commentable(): Model
    {
        return $this->commentableType::query()->findOrFail($this->commentableId);
    }

    public function reply(int $commentId): void
    {
        $this->replyTo = $commentId;
    }

    public function cancelReply(): void
    {
        $this->replyTo = null;
    }

    public function addComment(): void
    {
        $this->authorize('create', Comment::class);

        $validated = $this->validate([
            'body' => ['required', 'string', 'max:4000'],
            'replyTo' => ['nullable', 'integer', 'exists:comments,id'],
        ]);

        $this->commentable()->comments()->create([
            'user_id' => auth()->id(),
            'parent_id' => $validated['replyTo'] ?? null,
            'body' => $validated['body'],
        ]);

        $this->reset('body', 'replyTo');
        $this->dispatch('comment-added');
    }

    public function deleteComment(int $commentId): void
    {
        $comment = Comment::query()->findOrFail($commentId);

        $this->authorize('delete', $comment);

        $comment->delete();
    }

    public function render()
    {
        $commentable = $this->commentable();

        $comments = $commentable->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user', 'reactions'])
            ->latest()
            ->get();

        return view('livewire.comments', [
            'comments' => $comments,
            'total' => $commentable->comments()->count(),
        ]);
    }
}
