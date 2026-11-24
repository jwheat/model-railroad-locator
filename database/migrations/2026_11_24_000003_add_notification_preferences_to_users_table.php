<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('notify_comments')->default(true)->after('country');
            $table->boolean('notify_replies')->default(true)->after('notify_comments');
            $table->boolean('notify_follow_digest')->default(true)->after('notify_replies');
            $table->timestamp('digest_sent_at')->nullable()->after('notify_follow_digest');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'notify_comments',
                'notify_replies',
                'notify_follow_digest',
                'digest_sent_at',
            ]);
        });
    }
};
