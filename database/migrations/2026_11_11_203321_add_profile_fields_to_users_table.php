<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('name');
            $table->string('display_name')->nullable()->after('username');
            $table->text('bio')->nullable()->after('display_name');
            $table->string('avatar_path')->nullable()->after('bio');
            $table->boolean('is_admin')->default(false)->after('avatar_path');
            $table->string('city')->nullable()->after('is_admin');
            $table->string('region')->nullable()->after('city');
            $table->string('country', 2)->nullable()->after('region');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'display_name',
                'bio',
                'avatar_path',
                'is_admin',
                'city',
                'region',
                'country',
            ]);
        });
    }
};
