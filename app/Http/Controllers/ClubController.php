<?php

namespace App\Http\Controllers;

use App\Models\Club;
use Illuminate\View\View;

class ClubController extends Controller
{
    public function show(Club $club): View
    {
        $club->load(['members']);

        $layouts = $club->layouts()
            ->visible()
            ->withCount('posts')
            ->latest()
            ->get();

        $posts = $club->posts()
            ->published()
            ->with(['author', 'layout'])
            ->withCount('comments')
            ->latest('published_at')
            ->take(6)
            ->get();

        return view('clubs.show', [
            'club' => $club,
            'layouts' => $layouts,
            'posts' => $posts,
        ]);
    }
}
