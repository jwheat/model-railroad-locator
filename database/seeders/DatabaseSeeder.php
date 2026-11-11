<?php

namespace Database\Seeders;

use App\LayoutScale;
use App\LayoutVisibility;
use App\MembershipRole;
use App\Models\Club;
use App\Models\Comment;
use App\Models\Layout;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Tag;
use App\Models\User;
use App\PostStatus;
use App\ReactionType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Casey Jordan',
            'username' => 'casey',
            'display_name' => 'Casey Jordan',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'bio' => 'HO scale modeler, club treasurer, and serial benchwork re-builder. The Cascade Valley & Western has been under construction since 2016.',
            'city' => 'Portland',
            'region' => 'OR',
            'country' => 'US',
        ]);
        $admin->forceFill(['is_admin' => true])->save();

        $marta = User::factory()->create([
            'name' => 'Marta Reyes',
            'username' => 'marta',
            'display_name' => 'Marta Reyes',
            'email' => 'marta@example.com',
            'bio' => 'N scale modular modeler. I design track plans for small spaces and run the Tidewater Belt Line with my club.',
            'city' => 'Norfolk',
            'region' => 'VA',
            'country' => 'US',
        ]);

        $dev = User::factory()->create([
            'name' => 'Dev Patel',
            'username' => 'devpatel',
            'display_name' => 'Dev Patel',
            'email' => 'dev@example.com',
            'bio' => 'O scale, hand-laid track, and a stubborn preference for transition-era freight.',
            'city' => 'Ames',
            'region' => 'IA',
            'country' => 'US',
        ]);

        $ellen = User::factory()->create([
            'name' => 'Ellen Brooks',
            'username' => 'ellen',
            'display_name' => 'Ellen Brooks',
            'email' => 'ellen@example.com',
            'bio' => 'Scenery, trees, and weathering. My switching district is never quite finished.',
            'city' => 'Richmond',
            'region' => 'VA',
            'country' => 'US',
        ]);

        $sam = User::factory()->create([
            'name' => 'Sam Whitfield',
            'username' => 'sam',
            'display_name' => 'Sam Whitfield',
            'email' => 'sam@example.com',
            'bio' => 'G scale in the garden, N scale in the spare room. Yes, both.',
            'city' => 'Colorado Springs',
            'region' => 'CO',
            'country' => 'US',
        ]);

        $cascade = Club::factory()->create([
            'name' => 'Cascade Valley Model Railroad Club',
            'description' => 'A 60-member club in Portland with a permanent HO layout and a modular N scale group. Open runs on the first Saturday of the month.',
            'city' => 'Portland',
            'region' => 'OR',
            'country' => 'US',
            'website' => 'https://example.com/cascade',
            'is_verified' => true,
        ]);

        $tidewater = Club::factory()->create([
            'name' => 'Tidewater Modular Group',
            'description' => 'N scale modules built to the Tidewater standard. We set up at regional shows and host quarterly operating sessions.',
            'city' => 'Norfolk',
            'region' => 'VA',
            'country' => 'US',
            'is_verified' => true,
        ]);

        $prairie = Club::factory()->create([
            'name' => 'Prairie Lines Society',
            'description' => 'O scale and tinplate enthusiasts across central Iowa. Monthly swap table, annual open house.',
            'city' => 'Ames',
            'region' => 'IA',
            'country' => 'US',
        ]);

        $memberships = [
            [$cascade, $admin, MembershipRole::Owner],
            [$cascade, $ellen, MembershipRole::Member],
            [$tidewater, $marta, MembershipRole::Owner],
            [$tidewater, $sam, MembershipRole::Member],
            [$prairie, $dev, MembershipRole::Owner],
            [$prairie, $ellen, MembershipRole::Member],
        ];

        foreach ($memberships as [$club, $user, $role]) {
            $club->memberships()->create([
                'user_id' => $user->id,
                'role' => $role,
                'joined_at' => now()->subMonths(random_int(3, 36)),
            ]);
        }

        $layouts = collect([
            [
                'owner' => $admin,
                'club' => $cascade,
                'name' => 'Cascade Valley & Western',
                'description' => 'A double-deck HO layout set in the Pacific Northwest, circa 1974. The upper level follows the Columbia River gorge; the lower level is a hidden staging yard and a port switching district. Scenery is about 70% complete.',
                'scale' => LayoutScale::HO,
                'era' => '1970s transition',
                'city' => 'Portland',
                'region' => 'OR',
                'latitude' => 45.5152,
                'longitude' => -122.6784,
                'featured' => true,
            ],
            [
                'owner' => $marta,
                'club' => $tidewater,
                'name' => 'Tidewater Belt Line',
                'description' => 'A modular N scale layout that fits in a two-car garage. Six modules, a fiddle yard, and a harbor branch that is always the first thing visitors ask about.',
                'scale' => LayoutScale::N,
                'era' => 'Modern',
                'city' => 'Norfolk',
                'region' => 'VA',
                'latitude' => 36.8508,
                'longitude' => -76.2859,
                'featured' => true,
            ],
            [
                'owner' => $dev,
                'club' => $prairie,
                'name' => 'Prairie Junction',
                'description' => 'O scale, hand-laid code 125 track, and a stubborn commitment to 1953. The layout fills a 14x22 foot basement room with a point-to-point main line and a small interchange.',
                'scale' => LayoutScale::O,
                'era' => '1950s',
                'city' => 'Ames',
                'region' => 'IA',
                'latitude' => 42.0308,
                'longitude' => -93.6319,
                'featured' => false,
            ],
            [
                'owner' => $ellen,
                'club' => null,
                'name' => 'Blackwater Switching District',
                'description' => 'An HO switching layout in a spare bedroom. Industrial trackage, a runaround, and enough spotting opportunities to keep two operators busy for an evening.',
                'scale' => LayoutScale::HO,
                'era' => '1960s',
                'city' => 'Richmond',
                'region' => 'VA',
                'latitude' => 37.5407,
                'longitude' => -77.436,
                'featured' => false,
            ],
            [
                'owner' => $sam,
                'club' => $tidewater,
                'name' => 'Mountain Goat Route',
                'description' => 'A G scale garden railway winding through the backyard. Weathering is provided by actual weather. The grade is steeper than it should be and the trains are slower because of it.',
                'scale' => LayoutScale::G,
                'era' => 'Narrow gauge inspired',
                'city' => 'Colorado Springs',
                'region' => 'CO',
                'latitude' => 38.8339,
                'longitude' => -104.8214,
                'featured' => true,
            ],
            [
                'owner' => $marta,
                'club' => null,
                'name' => 'The Attic Line',
                'description' => 'A TT scale shelf layout built on two hollow-core doors. It is small, it is slow, and it is the only railroad I can leave set up year-round.',
                'scale' => LayoutScale::TT,
                'era' => '1960s',
                'city' => 'Madison',
                'region' => 'WI',
                'latitude' => 43.0731,
                'longitude' => -89.4012,
                'featured' => false,
            ],
        ])->mapWithKeys(function (array $data) {
            $layout = Layout::create([
                'user_id' => $data['owner']->id,
                'club_id' => $data['club']?->id,
                'name' => $data['name'],
                'description' => $data['description'],
                'scale' => $data['scale'],
                'era' => $data['era'],
                'city' => $data['city'],
                'region' => $data['region'],
                'country' => 'US',
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'visibility' => LayoutVisibility::Public,
                'is_featured' => $data['featured'],
            ]);

            return [$layout->slug => $layout];
        });

        $tags = collect([
            'benchwork', 'scenery', 'operations', 'DCC', 'scratchbuilding', 'n-scale', 'ho-scale',
        ])->mapWithKeys(fn (string $name) => [
            $name => Tag::create(['name' => $name]),
        ]);

        $stories = [
            [
                'author' => $admin,
                'layout' => 'cascade-valley-western',
                'club' => $cascade,
                'title' => 'Framing the upper level on Cascade Valley',
                'excerpt' => 'Six weekends of measuring, cutting, and second-guessing. The upper deck is finally in.',
                'body' => "## The plan\n\nThe upper level follows the Columbia River gorge from Portland east to The Dalles. I wanted a continuous run without a helix, so the grade climbs along the back wall at 1.5%.\n\n### What worked\n\n- 1x4 pine L-girders with plywood gussets are cheap and stiff.\n- A laser level paid for itself in one afternoon.\n- Building the upper deck as three removable sections made wiring much easier.\n\n### What I would change\n\nI used 5/8 inch plywood for the deck and it is heavier than it needs to be. Next time I would use 1/2 inch with more cross-bracing.\n\nThe first test train ran the full loop last night without a derailment. That is a good weekend.",
                'tags' => ['benchwork', 'ho-scale'],
            ],
            [
                'author' => $marta,
                'layout' => 'tidewater-belt-line',
                'club' => $tidewater,
                'title' => 'Tidewater Belt Line: our first operating session',
                'excerpt' => 'Eight operators, six modules, and one very confused switch crew. Lessons from a Saturday night ops session.',
                'body' => "We ran our first full operating session on the Belt Line last Saturday. Eight people showed up, which is about four more than I planned for.\n\n## What we learned\n\n1. **Label the throttles.** We spent ten minutes figuring out which controller belonged to which district.\n2. **Print car cards.** The handwritten ones were illegible by 8pm.\n3. **Have a dispatcher.** Without one, the harbor branch and the main line fight for the same switch.\n\nThe layout held up well. Only two derailments, both at the same unlevel module joint, which is now on the fix-it list.\n\nNext session is in six weeks. I am already rewriting the switch lists.",
                'tags' => ['operations', 'n-scale'],
            ],
            [
                'author' => $dev,
                'layout' => 'prairie-junction',
                'club' => $prairie,
                'title' => 'Hand-laying the yard throat',
                'excerpt' => 'Code 125 rail, a lot of patience, and a homemade gauge block.',
                'body' => "The yard throat at Prairie Junction is eleven turnouts in a row. I hand-laid all of them over three weeks.\n\n## Materials\n\n- Code 125 nickel silver rail\n- Wood ties stained with a dilute India ink wash\n- A homemade gauge block milled from aluminum\n\n## The process\n\nI spike every fourth tie first, check gauge, then fill in. It is slow, but the result is smooth and the frogs are exactly where I want them.\n\nThe first locomotive rolled through the whole ladder without a stutter. That never happens.",
                'tags' => ['scratchbuilding', 'operations'],
            ],
            [
                'author' => $ellen,
                'layout' => 'blackwater-switching-district',
                'club' => null,
                'title' => 'Scenery test: Blackwater Creek',
                'excerpt' => 'A small water feature, three shades of static grass, and a lot of experimenting.',
                'body' => "I built a one-foot square test piece before committing to the creek on the layout.\n\n## Recipe\n\n- Sculptamold over foam\n- Gloss medium over a dark brown base\n- Static grass in three lengths, applied in patches\n- A few pieces of driftwood from the back yard\n\nThe gloss medium is too shiny at this scale. I will try a matte varnish over a slightly lighter base on the real thing.\n\n```\nLesson: test on a scrap board first.\n```",
                'tags' => ['scenery', 'ho-scale'],
            ],
            [
                'author' => $sam,
                'layout' => 'mountain-goat-route',
                'club' => $tidewater,
                'title' => 'A garden railway in the high desert',
                'excerpt' => 'G scale, drip irrigation, and the ongoing battle with the local rabbits.',
                'body' => "The Mountain Goat Route is a loop of about 90 feet through the backyard, with a 4% grade that I regret.\n\n## Track\n\nI used brass track for the first season and switched to stainless steel. The brass needed cleaning every weekend; the stainless has been much better.\n\n## Plants\n\nDwarf junipers and creeping thyme have survived. Everything else has been a snack for the rabbits.\n\nThe railroad is not fine-scale. It is a toy train in the garden, and that is the point.",
                'tags' => ['scenery'],
            ],
            [
                'author' => $marta,
                'layout' => null,
                'club' => null,
                'title' => 'Benchwork on a budget',
                'excerpt' => 'You do not need a woodshop to build a layout. You need a square, a drill, and a plan.',
                'body' => "A friend asked how to start a layout in a small apartment. Here is the short version.\n\n## The frame\n\n- 1x3 pine ripped to consistent width\n- 1/4 inch plywood gussets\n- Wood glue and 1-1/4 inch screws\n\n## The top\n\n1/2 inch plywood, painted on both sides to control humidity. Foam on top for scenery.\n\n## The lesson\n\nSpend your money on a good square and a decent drill. The lumber can be cheap.",
                'tags' => ['benchwork'],
            ],
            [
                'author' => $dev,
                'layout' => 'prairie-junction',
                'club' => $prairie,
                'title' => 'Choosing an era (and changing your mind)',
                'excerpt' => 'I have rebuilt my roster three times. Here is what I wish I had known.',
                'body' => "I started in the modern era because the locomotives looked impressive. Then I realized I wanted cabooses, forty-foot boxcars, and a reason for a small yard.\n\n## What changed\n\nI sold most of the modern fleet and moved to 1953. The layout is smaller, the trains are shorter, and I am happier.\n\n## Advice\n\nPick the era you actually want to operate, not the one with the best models at the hobby shop. The models will follow.",
                'tags' => ['operations'],
            ],
            [
                'author' => $admin,
                'layout' => 'cascade-valley-western',
                'club' => $cascade,
                'title' => 'Five lessons from my first op session',
                'excerpt' => 'What went wrong, what went right, and what I am changing before the next session.',
                'body' => "We hosted twelve operators on the Cascade Valley last month. It was the first time the layout was pushed that hard.\n\n## The five lessons\n\n1. Staging capacity matters more than mainline length.\n2. Label everything. Then label it again.\n3. A fast clock is not a substitute for a schedule.\n4. Derailments happen at the same three spots. Fix those first.\n5. Feed your operators. It buys you an extra hour of patience.\n\nWe are already planning the next session.",
                'tags' => ['operations', 'DCC'],
            ],
        ];

        $posts = collect($stories)->map(function (array $story) use ($layouts, $tags) {
            $post = Post::create([
                'user_id' => $story['author']->id,
                'layout_id' => $story['layout'] ? $layouts[$story['layout']]->id : null,
                'club_id' => $story['club']?->id,
                'title' => $story['title'],
                'excerpt' => $story['excerpt'],
                'body' => $story['body'],
                'status' => PostStatus::Published,
                'published_at' => now()->subDays(random_int(2, 90)),
                'allow_comments' => true,
            ]);

            $post->tags()->attach(
                collect($story['tags'])->map(fn (string $tag) => $tags[$tag]->id)
            );

            return $post;
        });

        $comments = [
            [$posts[0], $marta, 'That laser level tip is dangerous. Now I want one.', null],
            [$posts[0], $ellen, 'How are you handling the transition between the two decks?', null],
            [$posts[1], $admin, 'The dispatcher advice is spot on. We added one last year and never looked back.', null],
            [$posts[1], $sam, 'Eight operators on six modules sounds like a great kind of chaos.', null],
            [$posts[2], $ellen, 'The gauge block is a smart idea. Did you mill it yourself?', null],
            [$posts[3], $dev, 'Matte varnish over a lighter base is the way to go. Try a drop of blue in the base coat too.', null],
            [$posts[4], $marta, 'A 4% grade in G scale is a rite of passage.', null],
        ];

        foreach ($comments as [$post, $user, $body, $parent]) {
            Comment::create([
                'commentable_type' => Post::class,
                'commentable_id' => $post->id,
                'user_id' => $user->id,
                'parent_id' => $parent?->id,
                'body' => $body,
                'created_at' => now()->subDays(random_int(1, 20)),
            ]);
        }

        $reactions = [
            [$posts[0], $marta, ReactionType::Love],
            [$posts[0], $dev, ReactionType::Like],
            [$posts[0], $ellen, ReactionType::Wow],
            [$posts[1], $admin, ReactionType::Like],
            [$posts[1], $sam, ReactionType::Love],
            [$posts[2], $ellen, ReactionType::Wow],
            [$posts[3], $dev, ReactionType::Like],
            [$posts[4], $marta, ReactionType::Haha],
            [$posts[5], $admin, ReactionType::Like],
            [$posts[6], $marta, ReactionType::Like],
            [$posts[7], $ellen, ReactionType::Love],
        ];

        foreach ($reactions as [$post, $user, $type]) {
            Reaction::create([
                'reactionable_type' => Post::class,
                'reactionable_id' => $post->id,
                'user_id' => $user->id,
                'type' => $type,
            ]);
        }

        $layoutReactions = [
            [$layouts['cascade-valley-western'], $marta, ReactionType::Love],
            [$layouts['cascade-valley-western'], $ellen, ReactionType::Like],
            [$layouts['tidewater-belt-line'], $admin, ReactionType::Like],
            [$layouts['prairie-junction'], $ellen, ReactionType::Wow],
            [$layouts['mountain-goat-route'], $dev, ReactionType::Love],
        ];

        foreach ($layoutReactions as [$layout, $user, $type]) {
            Reaction::create([
                'reactionable_type' => Layout::class,
                'reactionable_id' => $layout->id,
                'user_id' => $user->id,
                'type' => $type,
            ]);
        }

        $this->command->info('Seeded demo data. Sign in as admin@example.com / password.');
    }
}
