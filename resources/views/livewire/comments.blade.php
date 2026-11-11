<div>
<div class="mt-6 rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
    @auth
        <div class="flex gap-3">
            <x-avatar :user="auth()->user()" size="sm" class="!ring-stone-200" />
            <div class="flex-1">
                @if ($replyTo)
                    <p class="mb-2 flex items-center gap-2 text-xs font-medium text-emerald-800">
                        Replying to comment #{{ $replyTo }}
                        <button type="button" wire:click="cancelReply" class="text-stone-400 hover:text-stone-700">cancel</button>
                    </p>
                @endif
                <textarea wire:model="body" rows="3" placeholder="Share a tip, ask a question, or say thanks..."
                          class="w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm shadow-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200"></textarea>
                @error('body')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror
                <div class="mt-2 flex items-center justify-between">
                    <p class="text-xs text-stone-400">Be kind. No spam or self-promotion.</p>
                    <button type="button" wire:click="addComment" wire:loading.attr="disabled"
                            class="rounded-xl bg-emerald-900 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800 disabled:opacity-50">
                        Post comment
                    </button>
                </div>
            </div>
        </div>
    @else
        <p class="text-sm text-stone-600">
            <a href="{{ route('login') }}" class="font-semibold text-emerald-800 hover:underline">Sign in</a>
            to join the conversation.
        </p>
    @endauth
</div>

<div class="mt-6 space-y-6">
    @forelse ($comments as $comment)
        <div wire:key="comment-{{ $comment->id }}" class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <div class="flex gap-3">
                <x-avatar :user="$comment->user" size="sm" class="!ring-stone-200" />
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-baseline gap-x-2">
                        <a href="{{ route('members.show', $comment->user) }}" class="text-sm font-semibold text-stone-900 hover:text-emerald-800">
                            {{ $comment->user->displayName() }}
                        </a>
                        <span class="text-xs text-stone-400">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="mt-2 text-sm leading-6 whitespace-pre-line text-stone-700">{{ $comment->body }}</p>

                    <div class="mt-3 flex items-center gap-4 text-xs font-medium text-stone-500">
                        @auth
                            <button type="button" wire:click="reply({{ $comment->id }})" class="hover:text-emerald-800">Reply</button>
                        @endauth
                        @if (auth()->id() === $comment->user_id || auth()->user()?->isAdmin())
                            <button type="button" wire:click="deleteComment({{ $comment->id }})"
                                    wire:confirm="Delete this comment?"
                                    class="hover:text-rose-700">Delete</button>
                        @endif
                    </div>
                </div>
            </div>

            @if ($comment->replies->isNotEmpty())
                <div class="mt-4 space-y-4 border-l-2 border-stone-100 pl-4 sm:ml-11">
                    @foreach ($comment->replies as $reply)
                        <div wire:key="reply-{{ $reply->id }}" class="flex gap-3">
                            <x-avatar :user="$reply->user" size="sm" class="!ring-stone-200" />
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-baseline gap-x-2">
                                    <a href="{{ route('members.show', $reply->user) }}" class="text-sm font-semibold text-stone-900 hover:text-emerald-800">
                                        {{ $reply->user->displayName() }}
                                    </a>
                                    <span class="text-xs text-stone-400">{{ $reply->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="mt-1 text-sm leading-6 whitespace-pre-line text-stone-700">{{ $reply->body }}</p>
                                @if (auth()->id() === $reply->user_id || auth()->user()?->isAdmin())
                                    <button type="button" wire:click="deleteComment({{ $reply->id }})"
                                            wire:confirm="Delete this reply?"
                                            class="mt-2 text-xs font-medium text-stone-500 hover:text-rose-700">Delete</button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @empty
        <p class="rounded-2xl border border-dashed border-stone-300 bg-white p-8 text-center text-sm text-stone-500">
            No comments yet. Start the conversation.
        </p>
    @endforelse
</div>
</div>
