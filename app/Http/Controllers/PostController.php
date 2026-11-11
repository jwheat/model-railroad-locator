<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $posts = Post::query()
            ->published()
            ->with(['author', 'layout'])
            ->withCount('comments')
            ->search($search)
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('posts.index', [
            'posts' => $posts,
            'search' => $search,
        ]);
    }

    public function show(Post $post): View
    {
        $this->authorize('view', $post);

        $post->load(['author', 'layout', 'club', 'tags', 'media'])
            ->loadCount('comments');

        $related = Post::query()
            ->published()
            ->where('user_id', $post->user_id)
            ->whereKeyNot($post->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('posts.show', [
            'post' => $post,
            'related' => $related,
        ]);
    }
}
