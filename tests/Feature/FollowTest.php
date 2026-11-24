<?php

namespace Tests\Feature;

use App\Livewire\FollowButton;
use App\Models\Layout;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_follow_and_unfollow_a_layout(): void
    {
        $user = User::factory()->create();
        $layout = Layout::factory()->create();

        Livewire::actingAs($user)
            ->test(FollowButton::class, [
                'followableType' => 'layout',
                'followableId' => $layout->id,
            ])
            ->call('toggle')
            ->assertSet('followableType', Layout::class);

        $this->assertDatabaseHas('follows', [
            'user_id' => $user->id,
            'followable_type' => Layout::class,
            'followable_id' => $layout->id,
        ]);

        Livewire::actingAs($user)
            ->test(FollowButton::class, [
                'followableType' => 'layout',
                'followableId' => $layout->id,
            ])
            ->call('toggle');

        $this->assertDatabaseMissing('follows', [
            'user_id' => $user->id,
            'followable_type' => Layout::class,
            'followable_id' => $layout->id,
        ]);
    }

    public function test_member_can_follow_another_member_but_not_themselves(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Livewire::actingAs($user)
            ->test(FollowButton::class, [
                'followableType' => 'member',
                'followableId' => $other->id,
            ])
            ->call('toggle');

        $this->assertDatabaseHas('follows', [
            'user_id' => $user->id,
            'followable_type' => User::class,
            'followable_id' => $other->id,
        ]);

        Livewire::actingAs($user)
            ->test(FollowButton::class, [
                'followableType' => 'member',
                'followableId' => $user->id,
            ])
            ->call('toggle');

        $this->assertDatabaseMissing('follows', [
            'user_id' => $user->id,
            'followable_type' => User::class,
            'followable_id' => $user->id,
        ]);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $layout = Layout::factory()->create();

        Livewire::test(FollowButton::class, [
            'followableType' => 'layout',
            'followableId' => $layout->id,
        ])
            ->call('toggle')
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('follows', 0);
    }

    public function test_a_member_can_only_follow_an_item_once(): void
    {
        $user = User::factory()->create();
        $layout = Layout::factory()->create();

        $layout->followers()->create(['user_id' => $user->id]);

        try {
            $layout->followers()->create(['user_id' => $user->id]);
        } catch (UniqueConstraintViolationException) {
            $this->assertDatabaseCount('follows', 1);

            return;
        }

        $this->fail('A duplicate follow was allowed.');
    }
}
