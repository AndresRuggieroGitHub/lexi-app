<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->char('code', 5)->primary();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('mother_tongue_code')->references('code')->on('languages')->nullOnDelete();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'role_id']);
        });

        Schema::create('user_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->char('language_code', 5);
            $table->string('level_cefr', 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('language_code')->references('code')->on('languages')->cascadeOnDelete();
            $table->unique(['user_id', 'language_code']);
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->char('language_code', 5)->nullable();
            $table->timestamps();

            $table->foreign('language_code')->references('code')->on('languages')->nullOnDelete();
            $table->unique(['name', 'language_code']);
        });

        Schema::create('words', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->char('language_code', 5);
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('cefr_level', 2)->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();

            $table->foreign('language_code')->references('code')->on('languages')->cascadeOnDelete();
            $table->unique(['text', 'language_code']);
        });

        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_word_id')->constrained('words')->cascadeOnDelete();
            $table->foreignId('target_word_id')->constrained('words')->cascadeOnDelete();
            $table->string('context_note')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['source_word_id', 'target_word_id']);
        });

        Schema::create('user_words', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('word_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('new');
            $table->boolean('is_favorite')->default(false);
            $table->unsignedInteger('success_count')->default(0);
            $table->unsignedInteger('fail_count')->default(0);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('next_review_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'word_id']);
        });

        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->char('language_code', 5);
            $table->string('name');
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->foreign('language_code')->references('code')->on('languages')->cascadeOnDelete();
        });

        Schema::create('collection_words', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained('collections')->cascadeOnDelete();
            $table->foreignId('word_id')->constrained('words')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['collection_id', 'word_id']);
        });

        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('title')->nullable();
            $table->json('payload')->nullable();
            $table->string('source')->default('manual');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('exercise_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained('exercises')->cascadeOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->string('result_status')->nullable();
            $table->unsignedInteger('time_spent_seconds')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_attempts');
        Schema::dropIfExists('exercises');
        Schema::dropIfExists('collection_words');
        Schema::dropIfExists('collections');
        Schema::dropIfExists('user_words');
        Schema::dropIfExists('translations');
        Schema::dropIfExists('words');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('user_languages');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('roles');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['mother_tongue_code']);
        });

        Schema::dropIfExists('languages');
    }
};