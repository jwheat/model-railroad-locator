<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('club_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('scale')->index();
            $table->string('era')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('country', 2)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('visibility')->default('public');
            $table->boolean('is_featured')->default(false);
            $table->string('cover_image_path')->nullable();
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
            $table->index(['visibility', 'scale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layouts');
    }
};
