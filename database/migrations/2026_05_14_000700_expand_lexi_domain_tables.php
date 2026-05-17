<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['teacher_id', 'student_id']);
            $table->index(['student_id', 'status']);
        });

        Schema::create('exercise_templates', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('title')->nullable();
            $table->string('source')->default('manual');
            $table->unsignedSmallInteger('schema_version')->default(1);
            $table->json('payload')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['type', 'source']);
        });

        Schema::create('exercise_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('exercise_templates')->cascadeOnDelete();
            $table->unsignedInteger('item_order')->default(1);
            $table->string('item_type')->default('question');
            $table->text('question_text')->nullable();
            $table->text('correct_answer')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->unique(['template_id', 'item_order']);
        });

        Schema::create('exercise_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('exercise_items')->cascadeOnDelete();
            $table->string('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('option_order')->nullable();
            $table->timestamps();

            $table->index(['item_id', 'option_order']);
        });

        Schema::create('exercise_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('exercise_templates')->cascadeOnDelete();
            $table->char('assigned_language_code', 5)->nullable();
            $table->json('generated_payload')->nullable();
            $table->timestamps();

            $table->foreign('assigned_language_code')->references('code')->on('languages')->nullOnDelete();
            $table->index(['user_id', 'template_id']);
        });

        Schema::create('attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('exercise_attempts')->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('exercise_items')->nullOnDelete();
            $table->text('answer_text')->nullable();
            $table->json('answer_payload')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('points_obtained', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->index(['attempt_id', 'item_id']);
        });

        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $table->string('feature_key');
            $table->string('feature_value')->nullable();
            $table->timestamps();

            $table->unique(['plan_id', 'feature_key']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->string('provider')->nullable();
            $table->string('provider_payment_id')->nullable();
            $table->unsignedInteger('amount_cents')->default(0);
            $table->char('currency', 3)->default('EUR');
            $table->string('status')->default('paid');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['subscription_id', 'status']);
        });

        Schema::create('user_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->unsignedInteger('ai_generations_count')->default(0);
            $table->unsignedInteger('exercises_generated_count')->default(0);
            $table->unsignedInteger('exercise_attempts_count')->default(0);
            $table->unsignedInteger('saved_words_count')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_usage');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('plan_features');
        Schema::dropIfExists('attempt_answers');
        Schema::dropIfExists('exercise_instances');
        Schema::dropIfExists('exercise_options');
        Schema::dropIfExists('exercise_items');
        Schema::dropIfExists('exercise_templates');
        Schema::dropIfExists('teacher_student');
    }
};