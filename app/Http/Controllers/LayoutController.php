<?php

namespace App\Http\Controllers;

use App\Models\Layout;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class LayoutController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        return view('layouts.index');
    }

    public function show(Layout $layout): View
    {
        $this->authorize('view', $layout);

        $layout->load(['owner', 'club', 'media', 'tags'])
            ->loadCount('posts');

        $posts = $layout->posts()
            ->published()
            ->with('author')
            ->withCount('comments')
            ->latest('published_at')
            ->take(6)
            ->get();

        $related = Layout::query()
            ->visible()
            ->where('scale', $layout->scale)
            ->whereKeyNot($layout->id)
            ->with('owner')
            ->latest()
            ->take(3)
            ->get();

        return view('layouts.show', [
            'layout' => $layout,
            'posts' => $posts,
            'related' => $related,
        ]);
    }
}
