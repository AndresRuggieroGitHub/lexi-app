<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_languages', function (Blueprint $table) {
            $table->index(['user_id', 'is_active'], 'user_languages_user_active_index');
        });

        Schema::table('user_words', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'user_words_user_created_index');
            $table->index(['user_id', 'next_review_at'], 'user_words_user_review_index');
            $table->index('next_review_at', 'user_words_next_review_index');
            $table->index('status', 'user_words_status_index');
        });

        Schema::table('collections', function (Blueprint $table) {
            $table->index(['user_id', 'is_default', 'language_code', 'name'], 'collections_user_scope_index');
        });

        Schema::table('exercises', function (Blueprint $table) {
            $table->index(['type', 'title'], 'exercises_type_title_index');
        });

        Schema::table('exercise_attempts', function (Blueprint $table) {
            $table->index(['user_id', 'exercise_id'], 'exercise_attempts_user_exercise_index');
            $table->index(['user_id', 'created_at'], 'exercise_attempts_user_created_index');
            $table->index('created_at', 'exercise_attempts_created_index');
        });
    }

    public function down(): void
    {
        Schema::table('exercise_attempts', function (Blueprint $table) {
            $table->dropIndex('exercise_attempts_created_index');
            $table->dropIndex('exercise_attempts_user_created_index');
            $table->dropIndex('exercise_attempts_user_exercise_index');
        });

        Schema::table('exercises', function (Blueprint $table) {
            $table->dropIndex('exercises_type_title_index');
        });

        Schema::table('collections', function (Blueprint $table) {
            $table->dropIndex('collections_user_scope_index');
        });

        Schema::table('user_words', function (Blueprint $table) {
            $table->dropIndex('user_words_status_index');
            $table->dropIndex('user_words_next_review_index');
            $table->dropIndex('user_words_user_review_index');
            $table->dropIndex('user_words_user_created_index');
        });

        Schema::table('user_languages', function (Blueprint $table) {
            $table->dropIndex('user_languages_user_active_index');
        });
    }
};