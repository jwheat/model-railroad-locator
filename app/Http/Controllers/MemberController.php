<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function show(User $user): View
    {
        $posts = $user->posts()
            ->published()
            ->with('layout')
            ->withCount('comments')
            ->latest('published_at')
            ->paginate(6);

        $layouts = $user->layouts()
            ->visible()
            ->withCount('posts')
            ->latest()
            ->get();

        $clubs = $user->clubs()->get();

        return view('members.show', [
            'member' => $user,
            'posts' => $posts,
            'layouts' => $layouts,
            'clubs' => $clubs,
        ]);
    }
}
