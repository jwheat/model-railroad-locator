<?php

namespace Tests\Feature;

use App\Livewire\PostEditor;
use App\Models\Post;
use App\Models\User;
use App\PostStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PostEditorTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_create_and_publish_a_story(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(PostEditor::class)
            ->set('title', 'My first build report')
            ->set('body', '## Benchwork\n\nWe started framing today.')
            ->call('publish')
            ->assertHasNoErrors()
            ->assertRedirect();

        $post = Post::query()->firstWhere('title', 'My first build report');

        $this->assertNotNull($post);
        $this->assertSame($user->id, $post->user_id);
        $this->assertSame('my-first-build-report', $post->slug);
        $this->assertSame(PostStatus::Published, $post->status);
        $this->assertNotNull($post->published_at);
    }

    public function test_body_is_required_to_publish_but_not_to_save_a_draft(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(PostEditor::class)
            ->set('title', 'Work in progress')
            ->call('publish')
            ->assertHasErrors(['body' => 'required']);

        Livewire::actingAs($user)
            ->test(PostEditor::class)
            ->set('title', 'Work in progress')
            ->call('saveDraft')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('posts', [
            'title' => 'Work in progress',
            'status' => 'draft',
        ]);
    }

    public function test_member_can_upload_multiple_photos_with_a_story(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(PostEditor::class)
            ->set('title', 'Photo essay')
            ->set('body', 'A tour of the yard.')
            ->set('photos', [
                UploadedFile::fake()->image('yard.jpg'),
                UploadedFile::fake()->image('throat.jpg'),
                UploadedFile::fake()->image('roundhouse.jpg'),
            ])
            ->call('publish')
            ->assertHasNoErrors()
            ->assertRedirect();

        $post = Post::query()->firstWhere('title', 'Photo essay');

        $this->assertNotNull($post);
        $this->assertSame(3, $post->media()->where('type', 'image')->count());

        foreach ($post->media()->where('type', 'image')->get() as $photo) {
            Storage::disk('public')->assertExists($photo->path);
        }
    }

    public function test_member_can_remove_a_photo_from_a_story(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $photo = $post->media()->create([
            'user_id' => $user->id,
            'type' => 'image',
            'disk' => 'public',
            'path' => 'posts/old-photo.jpg',
        ]);
        Storage::disk('public')->put('posts/old-photo.jpg', 'image-bytes');

        Livewire::actingAs($user)
            ->test(PostEditor::class, ['post' => $post])
            ->call('removePhoto', $photo->id)
            ->call('saveDraft')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseMissing('media', ['id' => $photo->id]);
        Storage::disk('public')->assertMissing('posts/old-photo.jpg');
    }

    public function test_member_cannot_edit_another_members_story(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        Livewire::actingAs($other)
            ->test(PostEditor::class, ['post' => $post])
            ->assertForbidden();
    }
}
