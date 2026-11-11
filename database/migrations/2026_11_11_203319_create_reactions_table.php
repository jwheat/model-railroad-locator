<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->morphs('reactionable');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->timestamps();

            $table->unique(['reactionable_type', 'reactionable_id', 'user_id'], 'reactions_unique');
            $table->index(['reactionable_type', 'reactionable_id', 'type'], 'reactions_type_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};
