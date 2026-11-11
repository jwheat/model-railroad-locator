<?php

namespace App\Livewire;

use App\Models\Club;
use App\Models\Layout;
use App\Models\Post;
use App\PostStatus;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout as LayoutAttribute;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

#[LayoutAttribute('components.layouts.app')]
class PostEditor extends Component
{
    use AuthorizesRequests, WithFileUploads;

    #[Locked]
    public ?int $postId = null;

    public string $title = '';

    public string $excerpt = '';

    public string $body = '';

    public string $status = 'draft';

    public ?int $layoutId = null;

    public ?int $clubId = null;

    public $cover = null;

    public ?string $videoUrl = null;

    public function mount(?Post $post = null): void
    {
        if ($post?->exists) {
            $this->authorize('update', $post);

            $this->postId = $post->id;
            $this->title = $post->title;
            $this->excerpt = (string) $post->excerpt;
            $this->body = $post->body;
            $this->status = $post->status->value;
            $this->layoutId = $post->layout_id;
            $this->clubId = $post->club_id;
            $this->videoUrl = $post->media()->where('type', 'video_embed')->value('url');
        } else {
            $this->authorize('create', Post::class);
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(bool $publishing = false): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => [$publishing ? 'required' : 'nullable', 'string', 'max:100000'],
            'layoutId' => ['nullable', 'integer', 'exists:layouts,id'],
            'clubId' => ['nullable', 'integer', 'exists:clubs,id'],
            'cover' => ['nullable', 'image', 'max:5120'],
            'videoUrl' => ['nullable', 'url', 'max:2048'],
        ];
    }

    public function saveDraft(): void
    {
        $this->saveDraftOrPublish(false);
    }

    public function publish(): void
    {
        $this->saveDraftOrPublish(true);
    }

    private function saveDraftOrPublish(bool $publishing): void
    {
        $this->validate($this->rules($publishing));

        $attributes = [
            'title' => $this->title,
            'excerpt' => $this->excerpt ?: null,
            'body' => $this->body,
            'layout_id' => $this->layoutId,
            'club_id' => $this->clubId,
            'status' => $publishing ? PostStatus::Published : PostStatus::Draft,
            'published_at' => $publishing ? now() : null,
        ];

        if ($this->cover) {
            $attributes['cover_image_path'] = $this->cover->store('posts', 'public');
        }

        if ($this->postId) {
            $post = Post::findOrFail($this->postId);
            $this->authorize('update', $post);
            $post->update($attributes);
        } else {
            $this->authorize('create', Post::class);
            $post = auth()->user()->posts()->create($attributes);
            $this->postId = $post->id;
        }

        $post->media()->where('type', 'video_embed')->delete();

        if (filled($this->videoUrl)) {
            $post->media()->create([
                'user_id' => auth()->id(),
                'type' => 'video_embed',
                'url' => $this->videoUrl,
            ]);
        }

        session()->flash('status', $publishing ? 'Story published.' : 'Draft saved.');

        $this->redirectRoute('posts.show', $post);
    }

    public function render()
    {
        return view('livewire.post-editor', [
            'layouts' => Layout::query()->visible()->orderBy('name')->get(['id', 'name']),
            'clubs' => Club::query()->orderBy('name')->get(['id', 'name']),
            'preview' => Str::markdown($this->body, [
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]),
        ]);
    }
}
