<?php

namespace App\Http\Controllers;

use App\Models\Layout;
use App\Models\Post;
use App\Models\User;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featuredLayouts = Layout::query()
            ->visible()
            ->with(['owner'])
            ->withCount('posts')
            ->where('is_featured', true)
            ->latest()
            ->take(3)
            ->get();

        if ($featuredLayouts->isEmpty()) {
            $featuredLayouts = Layout::query()
                ->visible()
                ->with(['owner'])
                ->withCount('posts')
                ->latest()
                ->take(3)
                ->get();
        }

        $latestPosts = Post::query()
            ->published()
            ->with(['author', 'layout'])
            ->withCount('comments')
            ->latest('published_at')
            ->take(6)
            ->get();

        return view('home', [
            'featuredLayouts' => $featuredLayouts,
            'latestPosts' => $latestPosts,
            'layoutCount' => Layout::query()->visible()->count(),
            'postCount' => Post::query()->published()->count(),
            'memberCount' => User::query()->count(),
        ]);
    }
}
