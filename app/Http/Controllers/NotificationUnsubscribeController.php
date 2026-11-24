<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class NotificationUnsubscribeController extends Controller
{
    private const PREFERENCES = [
        'comments' => 'notify_comments',
        'replies' => 'notify_replies',
        'follow_digest' => 'notify_follow_digest',
    ];

    public function __invoke(User $user, string $type): View
    {
        abort_unless(isset(self::PREFERENCES[$type]), 404);

        $user->forceFill([self::PREFERENCES[$type] => false])->save();

        return view('notifications.unsubscribed', ['type' => $type]);
    }
}
